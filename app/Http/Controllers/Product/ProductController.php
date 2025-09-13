<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Mail\ProductAssignedMail;
use App\Models\Client;
use App\Models\Comment;
use App\Models\GraphicProductTypes;
use App\Models\History;
use App\Models\MasterProductStages;
use App\Models\Product;
use App\Models\ProductTracking;
use App\Models\Team;
use App\Models\User;
use App\Services\HistoryLogger;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:product-lists-products|change-product-due-date-products|assign-product-dropdown-products|change-product-status-dropdown-products|edit-product-button-products|update-product-button-products|view-tracking-products|view-product-comment-button-products', ['only' => ['productLists']]);
        $this->middleware('permission:add-client-product-button-clients', ['only' => ['addProductView', 'storeProduct']]);
        $this->middleware('permission:delete-client-product-clients', ['only' => ['deleteProduct']]);
        $this->middleware('permission:assign-product-dropdown-products', ['only' => ['assignProductToMember']]);
        $this->middleware('permission:change-product-status-dropdown-products', ['only' => ['changeProductStatus']]);
        $this->middleware('permission:edit-product-button-products', ['only' => ['edit-product-button-products']]);
        $this->middleware('permission:change-product-due-date-products', ['only' => ['updateDueDate']]);
        $this->middleware('permission:update-product-button-products', ['only' => ['updateProduct']]);
    }

    #product lists datatable
    public function productLists(Request $request)
    {
        try {
            $clients = Client::where('is_active', 1)->pluck('name', 'id')->toArray();
            if ($request->ajax()) {
                $members = User::with('team')->where('status', 1)->get();
                $groupedMembers = $members->groupBy(function ($item) {
                    return $item->team->name ?? 'NOTEAM';
                });
                $products = Product::select('*')
                    ->with(['client', 'member', 'assignedBy'])
                    ->whereHas('client', function ($query) {
                        $query->where('is_active', 1);
                    })
                    ->orderBy('id', 'desc');
                if ($request->client_id) {
                    $products->where('client_id', $request->client_id);
                }

                return DataTables::of($products)
                    ->addIndexColumn()
                    ->editColumn('product_code', function ($row) {
                        $productType = $row->product_type == 1 ? 'Size chart' : 'Tech pack';
                        return $row->product_code . ' (' . $productType . ')';
                    })
                    ->editColumn('client_name', function ($row) {
                        return $row->client->name ?? 'N/A';
                    })
                    ->editColumn('start_date', function ($row) {
                        return $row->created_at ? $row->created_at->format('d-m-Y') : 'N/A';
                    })
                    ->editColumn('due_date', function ($row) {
                        return view('products.components.due-date-input', [
                            'due_date' => $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : '',
                            'product_id' => $row->id,
                        ])->render();
                    })
                    ->editColumn('assigned_member', function ($row) use ($members, $groupedMembers) {
                        $selected = $row->assignedMembers->pluck('id')->toArray();
                        return view('products.components.dropdown', [
                            'options' => $members,
                            'groupedOptions' => $groupedMembers,
                            'selected' => $selected,
                            'selectedProductId' => $row->id,
                            'user_id' => Auth::user()->id,
                        ])->render();
                    })
                    ->editColumn('product_status', function ($row) {
                        return view('products.components.status-dropdown', [
                            'user_id' => Auth::user()->id,
                            'row' => $row,
                        ])->render();
                    })
                    ->addColumn('action', function ($row) {
                        return view('products.components.action-buttons', [
                            'row' => $row,
                        ])->render();
                    })
                    ->rawColumns(['assigned_member', 'product_status', 'action', 'product_code', 'due_date'])
                    ->make(true);
            }
            return view('products.products')->with([
                'clients' => $clients,
                'selected_client_id' => $request->client_id,
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('productLists: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Product lists', 'alert-type' => 'error']);
        }
    }

    #assign product to member
    public function assignProductToMember(Request $request)
    {
        try {
            $syncData = [];
            $product = Product::findOrFail($request->product_id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found', 'alert-type' => 'error']);
            }
            $oldAssignedIds = $product->assignedMembers()->pluck('users.id')->toArray();
            $oldAssignedNames = $product->assignedMembers()->pluck('users.name')->toArray();
            foreach ($request->assigned_members ?? [] as $memberId) {
                $syncData[$memberId] = ['assigned_by' => $request->assigned_by];
            }
            if (empty($syncData)) {
                $product->product_status = 0;
            }
            if ($product->product_status == 0 && !empty($syncData)) {
                $product->product_status = 1;
            }
            $product->assignedMembers()->sync($syncData);
            $product->updated_by = Auth::user()->id;
            $product->save();

            $newAssignedIds = array_keys($syncData);
            $newAssignedNames = User::whereIn('id', array_keys($syncData))->pluck('name')->toArray();
            $newlyAddedUserIds = array_diff($newAssignedIds, $oldAssignedIds);
            #Send email to newly assigned users only
            if (!empty($newlyAddedUserIds)) {
                $newUsers = User::whereIn('id', $newlyAddedUserIds)->get();
                foreach ($newUsers as $user) {
                    $notes = 'You have been assigned a new product: ' . $product->product_code . ' by ' . Auth::user()->name . '. For more info please check your dashboard.';
                    History::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::user()->id,
                        'action' => 'Product Assign',
                        'changes' => null,
                        'note' => $notes,
                        'assign_to' => $user->id,
                    ]);
                    Mail::to($user->email)->queue(new ProductAssignedMail($user, $product, Auth::user()->name, $notes));
                }
            }
            #log histories
            HistoryLogger::historyLog(
                $product,
                'assigned members updated',
                [
                    'assigned_members' => [
                        'old' => $oldAssignedNames,
                        'new' => $newAssignedNames
                    ],
                ],
                Auth::user()->id,
                'Product assignee has been updated by the member: ' . Auth::user()->name . ' for the product code: ' . $product->product_code . ' of type: ' . ($product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );

            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Failed to assign product to member', 'alert-type' => 'error']);
            }
            return response()->json(['status' => true, 'message' => 'Product assigned successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('assignProductToMember: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to assign product to member', 'alert-type' => 'error']);
        }
    }

    #Change Product Status
    public function changeProductStatus(Request $request)
    {
        try {
            $product = Product::find($request->product_id);
            $oldStatus = $product->product_status == 0 ? 'Pending' : ($product->product_status == 1 ? 'In Progress' : ($product->product_status == 2 ? 'On Hold' : ($product->product_status == 3 ? 'Completed' : '')));
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found', 'alert-type' => 'error']);
            }
            $product->product_status = $request->product_status;
            $product->status_changed_by = $request->status_changed_by;
            $product->updated_by = Auth::user()->id;
            $product->save();
            $status = $request->product_status == 0 ? 'Pending' : ($request->product_status == 1 ? 'In Progress' : ($request->product_status == 2 ? 'On Hold' : ($request->product_status == 3 ? 'Completed' : '')));
            #log histories
            HistoryLogger::historyLog(
                $product,
                'Product Status Update',
                [
                    'product_status' => [
                        'old' => $oldStatus,
                        'new' => $status
                    ],
                ],
                Auth::user()->id,
                'Product Status has been changed by the user: ' . Auth::user()->name . ' for the product code: ' . $product->product_code . ' of type: ' . ($product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );
            return response()->json(['status' => true, 'message' => 'The product status has been updated to ' . $status, 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('changeProductStatus: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to change the status of product', 'alert-type' => 'error']);
        }
    }

    #add product view
    public function addProductView($clientid = null)
    {
        try {
            $client = Client::where(['clientid' => $clientid, 'is_active' => 1])->first();
            if (!$client) {
                return redirect()->route('clients')->with(['message' => 'Please activate the client before adding products', 'alert-type' => 'error']);
            }
            #team and member details
            $teamDetails = Team::pluck('name', 'id')->toArray();
            # Product's graphic types
            $graphicProductTypes = GraphicProductTypes::pluck('name', 'id')->toArray();
            return view('products.addproduct')->with([
                'teamDetails' => $teamDetails,
                'graphicProductTypes' => $graphicProductTypes,
                'clientid' => $client->id,
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('addProductView: ' . $e->getMessage());
            return redirect()->route('clients')->with(['message' => 'Filed to view add product', 'alert-type' => 'error']);
        }
    }

    #store product
    public function storeProduct(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $syncData = [];
            $trimmedProductCode = preg_replace('/\s+/', ' ', trim($request->product_code));
            $slug = Str::slug($trimmedProductCode, '-');
            $productData = [
                'client_id' => $request->clientid,
                'product_type' => $request->product_type,
                'product_code' => strtoupper($trimmedProductCode),
                'slug' => $slug,
                'product_description' => $request->description,
                'graphic_type' => $request->graphic_product_type,
                'assigned_team' => $request->team_id,
                'assigned_member' => $request->member_id,
                'product_status' => $request->team_id && $request->member_id ? 1 : 0,
                'assigned_by' => Auth::user()->id,
            ];
            //check if product code already exists
            $existingProducts = Product::where('slug', $slug)->get();
            if ($existingProducts->isNotEmpty()) {
                $hasSizeChart = $existingProducts->contains('product_type', 1);
                $hasTechPack  = $existingProducts->contains('product_type', 2);
                // Prevent adding if both types already exist
                if ($hasSizeChart && $hasTechPack) {
                    DB::rollBack();
                    return redirect()->back()->with([
                        'message' => 'Both size chart and tech pack already exist for this product code',
                        'alert-type' => 'warning'
                    ])->withInput();
                }
                // Prevent duplicate of existing type
                if (($hasSizeChart && $request->product_type == 1) || ($hasTechPack && $request->product_type == 2)) {
                    DB::rollBack();
                    return redirect()->back()->with([
                        'message' => 'This product type already exists for the given product code: ' . $trimmedProductCode,
                        'alert-type' => 'info'
                    ])->withInput();
                }
            }
            //create product
            $product = Product::create($productData);
            if (!$product) {
                DB::rollBack();
                return redirect()->back()->with(['message' => 'Failed to add product', 'alert-type' => 'error'])->withInput();
            }
            if (!empty($request->member_id)) {
                $syncData[$request->member_id] = ['assigned_by' => Auth::user()->id];
            }
            $product->assignedMembers()->sync($syncData);
            //create product tracking
            $productTracking = MasterProductStages::orderby('sequence', 'ASC')->get();
            if ($productTracking->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->with(['message' => 'No product stages found', 'alert-type' => 'error'])->withInput();
            }
            foreach ($productTracking as $stage) {
                ProductTracking::create([
                    'product_id' => $product->id,
                    'stage_id' =>  $stage->id,
                    'product_stage' => $stage->name,
                    'stage_type' => $stage->type,
                ]);
            }
            if (!$productTracking) {
                DB::rollBack();
                return redirect()->back()->with(['message' => 'Failed to create product tracking', 'alert-type' => 'error'])->withInput();
            }
            // Upload product files
            $fileUploadController = new DocumentController();
            if (isset($request->uploaded_files) && !empty($request->uploaded_files)) {
                $files = json_decode($request->uploaded_files) ?? [];
                $uploadSuccess = $fileUploadController->saveProductFileDetails($files, $product->id);
                if ($uploadSuccess == false) {
                    DB::rollBack();
                    return redirect()->back()->with(['message' => 'Failed to save product details', 'alert-type' => 'error'])->withInput();
                }
            }
            #Send email to newly assigned users only
            if (!empty($request->member_id)) {
                $newUsers = User::where('id', $request->member_id)->get();
                foreach ($newUsers as $user) {
                    $notes = 'You have been assigned a new product: ' . $product->product_code . ' by ' . Auth::user()->name . '. For more info please check your dashboard.';
                    History::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::user()->id,
                        'action' => 'Product Assign',
                        'changes' => null,
                        'note' => $notes,
                        'assign_to' => $user->id,
                    ]);
                    Mail::to($user->email)->queue(new ProductAssignedMail($user, $product, Auth::user()->name, $notes));
                }
            }

            #create History
            $status = $product->product_status == 0 ? 'Pending' : ($product->product_status == 1 ? 'In Progress' : ($product->product_status == 2 ? 'On Hold' : ($product->product_status == 3 ? 'Completed' : '')));
            $assignedMember = User::whereIn('id', array_keys($syncData))->pluck('name')->toArray();
            $changes = [
                'product_code' => $product->product_code,
                'product_type' => $product->product_type == 1 ? 'Size Chart' : 'Tech Pack',
                'product_description' => $product->product_description,
                'graphic_type' => json_encode($product->graphic_type),
                'assigned_members' => json_encode($assignedMember),
                'product_status' => $status,
            ];
            #log histories
            if (isset($request->uploaded_files) && !empty($request->uploaded_files)) {
                $historyMessage = 'Product created by the user: ' . Auth::user()->name . ' for the client: ' . $product->client->name . ' with attachments.';
            } else {
                $historyMessage = 'Product created by the user: ' . Auth::user()->name . ' for the client: ' . $product->client->name;
            }
            HistoryLogger::historyLog(
                $product,
                'Product Created',
                $changes,
                Auth::user()->id,
                $historyMessage,
            );
            DB::commit();
            return redirect()->route('product.lists')->with(['message' => 'Product added successfully!', 'alert-type' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('exception')->error('storeProduct: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return redirect()->back()->with(['message' => 'Failed to add product', 'alert-type' => 'error'])->withInput();
        }
    }

    #delete Product Details
    public function deleteProduct($productId)
    {
        DB::beginTransaction();
        try {
            $fileUploadController = new DocumentController();
            //check if product code already exists
            $existingProducts = Product::where('id', $productId)->first();
            //create product
            if (!$existingProducts) {
                DB::rollBack();
                return redirect()->back()->with(['message' => 'Failed to delete product', 'alert-type' => 'error'])->withInput();
            }

            #log histories
            $changes = [
                'product_code' => $existingProducts->product_code,
                'product_type' => $existingProducts->product_type == 1 ? 'Size Chart' : 'Tech Pack',
            ];
            HistoryLogger::historyLog(
                $existingProducts,
                'Product Deleted',
                $changes,
                Auth::user()->id,
                'Product has been deleted by : ' . Auth::user()->name,
            );

            #delete product
            $existingProducts->delete();

            #Product Trackings
            ProductTracking::where('product_id', $productId)->delete();
            // delete product files
            $fileUploadController->deleteProductFiles($productId);
            #delete Comment Section
            Comment::where('product_id', $productId)->delete();
            #delete comment file
            $fileUploadController->deleteCommentFiles($productId);
            DB::commit();
            return redirect()->back()->with(['message' => 'Product deleted successfully!', 'alert-type' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('exception')->error('deleteProduct: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($productId));
            return redirect()->back()->with(['message' => 'Failed to delete product', 'alert-type' => 'error'])->withInput();
        }
    }

    #Edit Product Details
    public function editProductDetails($slug)
    {
        $sizeChart = $techPack = '';
        try {
            $type = request('type');
            if (empty($type)) {
                $productDetails = Product::where(['slug' => $slug])->with(['documents'])->first();
                $type = $productDetails->product_type;
            } else {
                $productDetails = Product::where(['product_type' => $type, 'slug' => $slug])->with(['documents'])->first();
            }
            $productDetails = Product::where(['product_type' => $type, 'slug' => $slug])->with(['documents', 'team', 'member'])->first();
            $graphicProductTypes = GraphicProductTypes::pluck('name', 'id')->toArray();
            if ($productDetails) {
                #gropued files
                $groupedDocuments = $productDetails->documents->where('is_deleted', 0)->groupBy(function ($item) {
                    return $item->stage_id ? 'stage' : 'product';
                });
                $stageDocuments = !empty($groupedDocuments['stage']) ? $groupedDocuments['stage'] : [];
                $productDocument = !empty($groupedDocuments['product']) ? $groupedDocuments['product'] : [];
                $teamName = isset($productDetails->team->name) ? $productDetails->team->name : '';
                $memberName = isset($productDetails->member->name) ? $productDetails->member->name : '';
                if ($productDetails->product_type == 1) {
                    $sizeChart = view('products.partials.size-chart-tab', ['product' => $productDetails, 'graphicTypes' => $graphicProductTypes, 'stageDocs' => $stageDocuments ?? [], 'productDocs' => $productDocument, 'team' => $teamName, 'member' => $memberName])->render();
                }
                if ($productDetails->product_type == 2) {
                    $techPack = view('products.partials.tech-pack-tab', ['product' => $productDetails, 'graphicTypes' => $graphicProductTypes, 'stageDocs' => $stageDocuments ?? [], 'productDocs' => $productDocument, 'team' => $teamName, 'member' => $memberName])->render();
                }
                return view('products.edit-product-details', ['product' => $productDetails, 'sizechart' => $sizeChart, 'techpack' => $techPack]);
            } else {
                $type = $type == 1 ? 'Size Chart' : 'Tech Pack';
                return redirect()->route('clients')->with(['message' => 'Please add product, No product details found for type: ' . $type, 'alert-type' => 'info']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('editProductDetails: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($slug));
            return redirect()->route('product.lists')->with(['message' => 'Failed to edit product details: ', 'alert-type' => 'error']);
        }
    }

    #View Product Details
    public function productDetails($slug)
    {
        $sizeChart = $techPack = '';
        try {
            $type = request('type');
            if (empty($type)) {
                $productDetails = Product::where(['slug' => $slug])->with(['documents', 'team', 'member'])->first();
                $type = $productDetails->product_type;
            } else {
                $productDetails = Product::where(['product_type' => $type, 'slug' => $slug])->with(['documents', 'team', 'member'])->first();
            }
            $graphicProductTypes = GraphicProductTypes::pluck('name', 'id')->toArray();
            if ($productDetails) {
                #gropued files
                $groupedDocuments = $productDetails->documents->where('is_deleted', 0)->groupBy(function ($item) {
                    return $item->stage_id ? 'stage' : 'product';
                });
                $stageDocuments = !empty($groupedDocuments['stage']) ? $groupedDocuments['stage'] : [];
                $productDocument = !empty($groupedDocuments['product']) ? $groupedDocuments['product'] : [];
                $teamName = isset($productDetails->team->name) ? $productDetails->team->name : '';
                $memberName = isset($productDetails->member->name) ? $productDetails->member->name : '';
                if ($productDetails->product_type == 1) {
                    $comments = Comment::with(['user.profile', 'documents'])->where(['product_id' => $productDetails->id, 'product_type' => $type])->orderBy('id', 'DESC')->get();
                    $sizeChart = view('products.viewpartials.size-chart-tab', ['prodtype' => $type, 'productId' => $productDetails->id, 'product' => $productDetails, 'graphicTypes' => $graphicProductTypes, 'stageDocs' => $stageDocuments ?? [], 'productDocs' => $productDocument, 'comments' => $comments ?? [], 'team' => $teamName, 'member' => $memberName])->render();
                }
                if ($productDetails->product_type == 2) {
                    $comments = Comment::with(['user.profile', 'documents'])->where(['product_id' => $productDetails->id, 'product_type' => $type])->orderBy('id', 'DESC')->get();
                    $techPack = view('products.viewpartials.tech-pack-tab', ['prodtype' => $type, 'productId' => $productDetails->id, 'product' => $productDetails, 'graphicTypes' => $graphicProductTypes, 'stageDocs' => $stageDocuments ?? [], 'productDocs' => $productDocument, 'comments' => $comments ?? [], 'team' => $teamName, 'member' => $memberName])->render();
                }
                return view('products.view-product-details', ['product' => $productDetails, 'sizechart' => $sizeChart, 'techpack' => $techPack]);
            } else {
                $type = $type == 1 ? 'Size Chart' : 'Tech Pack';
                return redirect()->route('clients')->with(['message' => 'Please add product, No product details found for type: ' . $type, 'alert-type' => 'info']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('productDetails: ' . $e->getMessage());
            return redirect()->route('product.lists')->with(['message' => 'Filed to view product details', 'alert-type' => 'error']);
        }
    }

    #update Due Date Of the Product
    public function updateDueDate(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'productId' => ['required', 'exists:products,id'],
                'due_date' => ['required', 'date'],
            ]);
            if ($validateData->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validateData->errors()->first(),
                    'alert-type' => 'error',
                ]);
            }
            $product = Product::findOrFail($request->productId);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Failed to update product due date', 'alert-type' => 'error']);
            }
            #old date
            $oldDate = $product->due_date ?? '--------';
            $product->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
            $product->updated_by = Auth::user()->id;
            $product->save();
            #log histories
            HistoryLogger::historyLog(
                $product,
                'Product Due Date Update',
                [
                    'product_DueDate' => [
                        'old' => $oldDate,
                        'new' => Carbon::parse($request->due_date)->format('Y-m-d')
                    ],
                ],
                Auth::user()->id,
                'Product Due Date has been changed by the user: ' . Auth::user()->name . ' for the product code: ' . $product->product_code . ' of type: ' . ($product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );
            return response()->json(['status' => true, 'message' => 'Product due date has been updated', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('updateDueDate: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to update product due date', 'alert-type' => 'error']);
        }
    }

    #update product details
    public function updateProduct(UpdateProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $product = Product::where(['slug' => $request->slug, 'product_type' => $request->type])->first();
            if (!$product) {
                return redirect()->route('product.lists')->with(['message' => 'Product not found.', 'alert-type' => 'error']);
            }

            #product old values
            $oldValue = $product->only(['product_description', 'graphic_type']);
            // Update product fields
            $product->product_description = $request->description;
            $product->graphic_type = $request->graphic_product_type;
            $product->updated_by = Auth::user()->id;
            $product->save();
            # Product new changes
            $changes = [];
            foreach (['product_description', 'graphic_type'] as $field) {
                if ($product->$field !== $oldValue[$field]) {
                    $changes[$field] = [
                        'old' => $oldValue[$field],
                        'new' => $product->$field,
                    ];
                }
            }
            #log histories
            HistoryLogger::historyLog(
                $product,
                'Product Updated',
                $changes,
                Auth::user()->id,
                'Product Edited by the user: ' . Auth::user()->name . ' for the product code: ' . $product->product_code . ' of type: ' . ($product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );
            DB::commit();
            return redirect()->route('product.lists')->with(['message' => 'Product updated successfully!', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('exception')->error('updateProduct: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return redirect()->back()->with(['message' => 'Failed to update product.', 'alert-type' => 'error'])->withInput();
        }
    }
}
