<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Mail\ProductAssignedMail;
use App\Models\Client;
use App\Models\Comment;
use App\Models\GraphicProductTypes;
use App\Models\History;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ProductUser;
use App\Models\Team;
use App\Services\HistoryLogger;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:task-lists-tasks|view-task-comment-button-tasks', ['only' => ['allTaskList']]);
    }
    #product lists datatable
    public function allTaskList(Request $request)
    {
        try {
            $routeName =  $request->route()->getName() ?? '';
            $clients = Client::where('is_active', 1)->pluck('name', 'id')->toArray();
            $teams = Team::pluck('name', 'id')->toArray();
            $users = User::where('status', 1)->where('email', '!=', config('global.useremail'))->pluck('name', 'id')->toArray();
            $status = [0 => 'Pending', 1 => 'In Progress', 2 => 'On Hold', 3 => 'Completed'];
            if ($request->ajax()) {
                $members = User::with('team')->where('status', 1)->get();
                $groupedMembers = $members->groupBy(function ($item) {
                    return $item->team->name ?? 'NOTEAM';
                });
                $products = ProductUser::with(['user.team', 'product.client', 'assignedBy'])
                    ->whereHas('product.client', function ($query) {
                        $query->where('is_active', 1);
                    })
                    ->orderBy('id', 'desc');
                #route filter    
                if ($routeName === 'mytasks') {
                    $products->where('user_id', Auth::id());
                }

                #custom request from form
                #product code
                if ($request->productcode) {
                    $products->whereHas('product', function ($query) use ($request) {
                        $query->where('product_code', 'like', '%' . $request->productcode . '%');
                    });
                }
                #client
                if ($request->client_id) {
                    $products->whereHas('product', function ($query) use ($request) {
                        $query->where('client_id', $request->client_id);
                    });
                }
                #team
                if ($request->team_id) {
                    $products->whereHas('user.team', function ($query) use ($request) {
                        $query->where('id', $request->team_id);
                    });
                }
                #member
                if ($request->user_id) {
                    $products->where('user_id', $request->user_id);
                }
                #status
                if (!is_null($request->status_id)) {
                    $products->whereHas('product', function ($query) use ($request) {
                        $query->where('product_status', $request->status_id);
                    });
                }

                return DataTables::of($products)
                    ->addIndexColumn()
                    ->editColumn('product_code', function ($row) {
                        $productType = $row->product->product_type == 1 ? 'Size chart' : 'Tech pack';
                        return $row->product->product_code . ' (' . $productType . ')';
                    })
                    ->editColumn('client_name', function ($row) {
                        return $row->product->client->name ?? 'N/A';
                    })
                    ->editColumn('start_date', function ($row) {
                        return $row->product->created_at ? $row->product->created_at->format('d-m-Y') : 'N/A';
                    })
                    ->editColumn('due_date', function ($row) {
                        return $row->product->due_date ? Carbon::parse($row->product->due_date)->format('d-m-Y') : 'N/A';
                    })
                    ->editColumn('assigned_team', function ($row) {
                        return $row->user->team->name ?? 'N/A';
                    })
                    ->editColumn('assigned_member', function ($row) use ($members, $groupedMembers) {
                        $selected = $row->user_id;
                        return view('tasks.components.dropdown', [
                            'options' => $members,
                            'groupedOptions' => $groupedMembers,
                            'selected' => $selected,
                            'selectedProductId' => $row->product_id,
                            'user_id' => Auth::user()->id,
                        ])->render();
                    })
                    ->editColumn('product_status', function ($row) {
                        $color = $this->statusColor($row->product->product_status);
                        return view('tasks.components.status-dropdown', [
                            'color' => $color,
                            'status' => $row->product->product_status,
                        ])->render();
                    })
                    ->addColumn('action', function ($row) {
                        $commentCount = Comment::where(['product_type' => $row->product->product_type, 'product_id' => $row->product->id])->count();
                        return view('tasks.components.comment', [
                            'slug' => $row->product->slug,
                            'type' => $row->product->product_type,
                            'count' => $commentCount
                        ])->render();
                    })
                    ->rawColumns(['assigned_member', 'product_status', 'action', 'product_code'])
                    ->make(true);
            }
            return view('tasks.products')->with([
                'clients' => $clients,
                'teams' => $teams,
                'members' => $users,
                'status' => $status,
                'route' => $routeName === 'tasks' ? route('tasks') : route('mytasks'),
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('taskList: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Task lists', 'alert-type' => 'error']);
        }
    }

    #assign product to member
    public function assignTaskProductToMember(Request $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found', 'alert-type' => 'error']);
            }
            $oldAssignedNames = $product->assignedMembers()->pluck('users.name')->toArray();
            $currentUserId = Auth::id();
            $newMemberId = $request->assigned_member;
            $product->assignedMembers()->detach($currentUserId);
            if ($newMemberId) {
                $product->assignedMembers()->syncWithoutDetaching([
                    $newMemberId => ['assigned_by' => $request->assigned_by]
                ]);
                #Send email to newly assigned users only
                $newAssignee = User::where('id', $newMemberId)->get();
                foreach ($newAssignee as $user) {
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
            $product->updated_by = Auth::user()->id;
            $product->save();
            $newAssigneeNames = $product->assignedMembers()->pluck('users.name')->toArray();
            #log histories
            $assignedMember = User::select('name')->where('id', $newMemberId)->first();
            HistoryLogger::historyLog(
                $product,
                'Assign Task',
                [
                    'assign_task' => [
                        'old' => $oldAssignedNames ?? [],
                        'new' => $newAssigneeNames ?? [],
                    ],
                ],
                Auth::user()->id,
                'Product assigned to ' . $assignedMember->name . ' by the member: ' . Auth::user()->name . ' for the product code: ' . $product->product_code . ' of type: ' . ($product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );

            return response()->json(['status' => true, 'message' => 'Product assigned successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('assignTaskProductToMember: Error-  ' . $e->getMessage() . ' | With the request details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to assign product to member', 'alert-type' => 'error']);
        }
    }

    #View Product Details
    public function taskProductDetails($slug)
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
                $groupedDocuments = $productDetails->documents->groupBy(function ($item) {
                    return $item->stage_id ? 'stage' : 'product';
                });
                $stageDocuments = !empty($groupedDocuments['stage']) ? $groupedDocuments['stage'] : [];
                $productDocument = !empty($groupedDocuments['product']) ? $groupedDocuments['product'] : [];
                $teamName = isset($productDetails->team->name) ? $productDetails->team->name : '';
                $memberName = isset($productDetails->member->name) ? $productDetails->member->name : '';
                if ($productDetails->product_type == 1) {
                    $comments = Comment::with(['user', 'documents'])->where(['product_id' => $productDetails->id, 'product_type' => $type])->orderBy('id', 'DESC')->get();
                    $sizeChart = view('tasks.viewpartials.size-chart-tab', ['prodtype' => $type, 'productId' => $productDetails->id, 'product' => $productDetails, 'graphicTypes' => $graphicProductTypes, 'stageDocs' => $stageDocuments ?? [], 'productDocs' => $productDocument, 'comments' => $comments ?? [], 'team' => $teamName, 'member' => $memberName])->render();
                }
                if ($productDetails->product_type == 2) {
                    $comments = Comment::with(['user', 'documents'])->where(['product_id' => $productDetails->id, 'product_type' => $type])->orderBy('id', 'DESC')->get();
                    $techPack = view('tasks.viewpartials.tech-pack-tab', ['prodtype' => $type, 'productId' => $productDetails->id, 'product' => $productDetails, 'graphicTypes' => $graphicProductTypes, 'stageDocs' => $stageDocuments ?? [], 'productDocs' => $productDocument, 'comments' => $comments ?? [], 'team' => $teamName, 'member' => $memberName])->render();
                }
                return view('tasks.view-product-details', ['product' => $productDetails, 'sizechart' => $sizeChart, 'techpack' => $techPack]);
            } else {
                $type = $type == 1 ? 'Size Chart' : 'Tech Pack';
                return redirect()->route('clients')->with(['message' => 'Please add product, No product details found for type: ' . $type, 'alert-type' => 'info']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('taskProductDetails: ' . $e->getMessage());
            return redirect()->route('tasks')->with(['message' => 'Filed to view product details', 'alert-type' => 'error']);
        }
    }

    #product status button color
    private function statusColor($selectedValue)
    {
        $backgroundColor = '';
        switch ($selectedValue) {
            case '0':
                $backgroundColor = '#D57C7C'; // Red
                break;
            case '1':
                $backgroundColor = '#709CC2'; // Blue
                break;
            case '2':
                $backgroundColor = '#CFB55E'; // Yellow
                break;
            case '3':
                $backgroundColor = '#76BF97'; // Green
                break;
        }
        return $backgroundColor;
    }
}
