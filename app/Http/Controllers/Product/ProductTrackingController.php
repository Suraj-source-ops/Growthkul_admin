<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductTracking;
use App\Services\HistoryLogger;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Catch_;
use Yajra\DataTables\Facades\DataTables;

class ProductTrackingController extends Controller
{
    #product tracking list
    public function productTrackingLists(Request $request)
    {
        try {
            $clients = Client::where('is_active', 1)->pluck('name', 'id')->toArray();
            if ($request->ajax()) {
                $products = Product::select('id', 'client_id', 'product_code', 'slug', 'product_type')
                    ->with([
                        'client',
                        'stages' => function ($query) {
                            $query->select('id', 'product_id', 'stage_id', 'product_stage', 'status')->orderBy('id', 'asc')->limit(3);
                        },
                        'stages.masterStage',
                    ])
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
                    ->editColumn('stage_first', function ($row) {
                        $stage = $row->stages[0] ?? null;
                        if (isset($row->stages) && $stage) {
                            return '<div class="toglle-edit-icons">
                                    <label class="switch">
                                        <input class="form-check-input check-status-css" type="checkbox"
                                            role="switch" id="statusSwitchStage-' . $stage['id'] . '" ' . ($stage['status'] == 1 ? 'checked' : '') . '
                                            onchange="changeStatus(' . $stage['id'] . ')"><span
                                            class="slider round"></span>
                                    </label>
                                    <p>' . $stage['product_stage'] . '</p>
                                </div>';
                        } else {
                            return 'No Stage Found';
                        }
                    })
                    ->editColumn('stage_second', function ($row) {
                        $stage = $row->stages[1] ?? null;
                        if (isset($row->stages) && $stage) {
                            return '<div class="toglle-edit-icons">
                                <label class="switch">
                                    <input class="form-check-input check-status-css" type="checkbox"
                                        role="switch" id="statusSwitchStage-' . $stage['id'] . '" ' . ($stage['status'] == 1 ? 'checked' : '') . '
                                        onchange="changeStatus(' . $stage['id'] . ')"><span
                                        class="slider round"></span>
                                </label>
                               <p>' . $stage['product_stage'] . '</p>
                            </div>';
                        } else {
                            return 'No Stage Found';
                        }
                    })
                    ->editColumn('stage_third', function ($row) {
                        $stage = $row->stages[2] ?? null;
                        if (isset($row->stages)) {
                            return '<div class="toglle-edit-icons">
                                <label class="switch">
                                    <input class="form-check-input check-status-css" type="checkbox"
                                        role="switch" id="statusSwitchStage-' . $stage['id'] . '" ' . ($stage['status'] == 1 ? 'checked' : '') . '
                                        onchange="changeStatus(' . $stage['id'] . ')"><span
                                        class="slider round"></span>
                                </label>
                                <p>' . $stage['product_stage'] . '</p>
                            </div>';
                        } else {
                            return 'No Stage Found';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="' . route('product.stages', ['productId' => $row->id]) . '" class="edit-comman-btn">View</a>';
                    })
                    ->rawColumns(['stage_first', 'stage_second', 'stage_third', 'product_code', 'action'])
                    ->make(true);
            }
            return view('products.product-traking-lists')->with([
                'clients' => $clients,
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('productTrackingLists: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch Product lists', 'alert-type' => 'error']);
        }
    }

    #update tracking status()
    public function changeStatus($stageId)
    {
        try {
            $productStage = ProductTracking::with('product')->find($stageId);
            if (!$productStage) {
                return response()->json(['status' => false, 'message' => 'Product stage not found.', 'alert-type' => 'error']);
            }
            $oldStatus = $productStage->status == 1 ? 'Completed' : 'Pending';
            $productStage->status = !$productStage->status;
            $productStage->updated_by = Auth::user()->id;
            if ($productStage->save()) {
                #log histories
                HistoryLogger::historyLog(
                    $productStage->product,
                    'Stage Status Update',
                    [
                        'stage_status' => [
                            'old' => $oldStatus,
                            'new' => $productStage->status == 1 ? 'Completed' : 'Pending'
                        ],
                    ],
                    Auth::user()->id,
                    'Product Stage - ' . $productStage->product_stage . ', Status has been changed by the user: ' . Auth::user()->name . ' for the product code: ' . $productStage->product->product_code . ' of type: ' . ($productStage->product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
                );
                #end notification
                return response()->json(['status' => true, 'message' => 'Product stage status updated successfully.', 'alert-type' => 'success']);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to update Product stage status.', 'alert-type' => 'error']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('changeStatus: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($stageId));
            return response()->json(['status' => false, 'message' => 'Failed to update Product stage status.', 'alert-type' => 'error']);
        }
    }

    #Product Tracking view page and update page
    public function productTracking(Request $request, $productId)
    {
        try {
            $productCode = Product::select('product_code')->find($productId);
            if ($request->ajax()) {
                $stageQuery = ProductTracking::where('product_id', $productId)->orderBy('id', 'ASC');
                return DataTables::of($stageQuery)
                    ->addIndexColumn()
                    ->editColumn('stage_name', function ($row) {
                        return $row->product_stage ?? 'N/A';
                    })
                    ->editColumn('estimate_date', function ($row) {
                        $currentDate = Carbon::now();
                        return view('products.components.stages.due-date-input', [
                            'estimate_date' => $row->estimate_date ? Carbon::parse($row->estimate_date)->format('Y-m-d') : '',
                            'stageid' => $row->id,
                            'today' => $currentDate->format('Y-m-d')
                        ])->render();
                    })
                    ->editColumn('status', function ($row) use ($productId) {
                        if (!empty($row->stage_type) && $row->stage_type == 1) {
                            return view('products.components.stages.choose-file', [
                                'status' => $row->status ?? 0,
                                'stageid' => $row->id,
                                'productid' => $productId,
                            ])->render();
                        } else {
                            return view('products.components.stages.toggle-button', [
                                'status' => $row->status ?? 0,
                                'stageid' => $row->id,
                            ])->render();
                        }
                    })
                    ->editColumn('notes', function ($row) {
                        return view('products.components.stages.notes', [
                            'notes' => $row->notes ?? '',
                            'stageid' => $row->id,
                        ])->render();
                    })
                    ->rawColumns(['stage_name', 'estimate_date', 'status', 'notes'])
                    ->make(true);
            }
            return view('products.product-tracking', ['productId' => $productId, 'code' => $productCode->product_code ?? '']);
        } catch (Exception $e) {
            Log::channel('exception')->error('productTracking: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch product stages', 'alert-type' => 'error']);
        }
    }

    #update estimate date
    public function updateStageEstimateDate(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'stageId'  => ['required', 'exists:product_trackings,id'],
                'est_date' => ['required', 'date'],
            ]);
            // Handle validation failure
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first(), 'alert-type' => 'error']);
            }
            $productStage = ProductTracking::with('product')->findOrFail($request->stageId);
            $oldValue = $productStage->estimate_date;
            $productStage->estimate_date = Carbon::parse($request->est_date)->format('Y-m-d');
            $productStage->updated_by = Auth::user()->id;
            $productStage->save();
            #log histories
            HistoryLogger::historyLog(
                $productStage->product,
                'Stage Estimate Date Update',
                [
                    'stage_estimate_date' => [
                        'old' => $oldValue ?? '--------',
                        'new' => $productStage->estimate_date
                    ],
                ],
                Auth::user()->id,
                'Product Stage - ' . $productStage->product_stage . ', Estimate date has been updated by the user: ' . Auth::user()->name . ' for the product code: ' . $productStage->product->product_code . ' of type: ' . ($productStage->product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );
            #end notification
            return response()->json(['status' => true, 'message' => 'Stage estimate date has been updated', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('updateStageEstimateDate: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to update estimate date', 'alert-type' => 'error']);
        }
    }

    #update estimate date
    public function updateNotes(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'stageId' => ['required', 'exists:product_trackings,id'],
                'note'    => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first(), 'alert-type' => 'error',]);
            }
            $trimmedProductNotes = preg_replace('/\s+/', ' ', trim($request->note));
            $productStage = ProductTracking::with('product')->findOrFail($request->stageId);
            $oldDescription = $productStage->notes;
            $productStage->notes = $trimmedProductNotes;
            $productStage->updated_by = Auth::user()->id;
            $productStage->save();

            #log histories
            HistoryLogger::historyLog(
                $productStage->product,
                'Stage Description Update',
                [
                    'stage_description' => [
                        'old' => $oldDescription ?? 'NA',
                        'new' => $productStage->notes
                    ],
                ],
                Auth::user()->id,
                'Product Stage - ' . $productStage->product_stage . ', Description has been updated by the user: ' . Auth::user()->name . ' for the product code: ' . $productStage->product->product_code . ' of type: ' . ($productStage->product->product_type == 1 ? 'Size Chart' : 'Tech Pack'),
            );
            #end notification
            return response()->json(['status' => true, 'message' => 'Notes has been updated', 'alert-type' => 'success']);
        } catch (\Throwable $e) {
            Log::channel('exception')->error('productTracking: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            // return redirect()->back()->with(['status' => false, 'message' => 'Failed to update notes', 'alert-type' => 'error']);
            return response()->json(['status' => false, 'message' => 'Failed to update notes', 'alert-type' => 'error']);
        }
    }

    #upload files mutlti
    public function uploadStageFile(Request $request)
    {
        try {
            // Validate request inputs
            $messages = [
                'files.*.mimes' => 'The uploaded file must be a file of type: jpg, jpeg, png, pdf, doc, docx',
            ];
            $validator = Validator::make($request->all(), [
                'product_id' => ['required', 'exists:products,id'],
                'stage_id'   => ['required', 'exists:product_trackings,id'],
                'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            ], $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'alert-type' => 'error',
                ]);
            }
            $files = $request->file('files');
            $docController = new DocumentController;
            $uploadStatus = $docController->uploadProductFiles(Auth::user()->id, $request->product_id, $request->stage_id, $files, 'stagefiles');
            if ($uploadStatus) {
                $productStage = ProductTracking::findOrFail($request->stage_id);
                $productStage->updated_by = Auth::user()->id;
                $productStage->save();
                return response()->json(['status' => true, 'message' => 'Files uploaded successfully', 'alert-type' => 'success']);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to upload files', 'alert-type' => 'error']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('uploadStageFile: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to upload files', 'alert-type' => 'error']);
        }
    }
}
