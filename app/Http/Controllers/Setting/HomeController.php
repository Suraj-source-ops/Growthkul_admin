<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\GraphicProductTypes;
use App\Models\MasterProductStages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{

    #product's graphic types add and list view
    public function graphicProductTypes(Request $request)
    {
        try {
            if ($request->ajax()) {
                $graphicTypeLists = GraphicProductTypes::select('*')->orderBy('id', 'desc');
                return DataTables::of($graphicTypeLists)
                    ->addIndexColumn()
                    ->editColumn('action', function ($row) {
                        return '<div class="">
                            <a href="javascript:void(0);" onclick="deleteGraphicProductType(' . $row->id . ')" class="text-danger" title="Delete">
                                <i class="far fa-trash-alt tras-icons"></i>
                            </a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('settings.graphicProductTypes.graphis-product-types');
        } catch (Exception $e) {
            Log::channel('exception')->error('graphicProductTypes: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch graphic types', 'alert-type' => 'error']);
        }
    }

    #add product's graphic type
    public function addGraphicProductType(Request $request)
    {
        try {
            if (!$request->has('name') || empty($request->input('name'))) {
                return Redirect::back()->with(['message' => 'Please enter product\'s graphic name', 'alert-type' => 'error']);
            }

            GraphicProductTypes::create([
                'name' => $request->input('name'),
            ]);
            return Redirect::back()->with(['message' => "product's graphic type added successfully", 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('addGraphicProductType: ' . $e->getMessage());
            return Redirect::back()->with(['message' => 'Failed to add graphic type', 'alert-type' => 'error']);
        }
    }

    #delete product's graphic type
    public function deleteGraphicProductType($id)
    {
        Log::channel('daily')->info('deleteGraphicProductType: Attempting to delete graphic product type of ID ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $type = GraphicProductTypes::find($id);
            if (!$type) {
                return response()->json(['status' => false, 'message' => 'Graphic type not found', 'alert-type' => 'error']);
            }
            $type->delete();
            return response()->json(['status' => true, 'message' => 'Graphic type deleted successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteGraphicProductType: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to delete graphic type', 'alert-type' => 'error']);
        }
    }


    #Master Stages List Section
    #product's graphic types add and list view
    public function masterStages(Request $request)
    {
        try {
            if ($request->ajax()) {
                $masterStageList = MasterProductStages::select('*')->orderBy('sequence');
                return DataTables::of($masterStageList)
                    ->addIndexColumn()
                    ->orderColumn('sequence', 'sequence $1')
                    ->addColumn('sequence', function ($row) {
                        return $row->sequence;
                    })
                    ->editColumn('type', function ($row) {
                        return $row->type == 1 ? 'File' : 'Toggle Button';
                    })
                    ->editColumn('action', function ($row) {
                        return '<div class="">
                                   <a href="javascript:void(0);" onclick="deleteStages(' . $row->id . ')" class="text-danger" title="Delete">
                                        <i class="far fa-trash-alt tras-icons"></i>
                                   </a>
                                </div>';
                    })
                    ->rawColumns(['sequence', 'type', 'action'])
                    ->make(true);
            }
            return view('settings.masterpPoductTrackStages.masterStage');
        } catch (Exception $e) {
            Log::channel('exception')->error('masterStages: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch stage lists', 'alert-type' => 'error']);
        }
    }

    #add product's graphic types
    public function addStages(Request $request)
    {
        Log::channel('daily')->info('addStages: Attempting to add stages by the user ' . Auth::user()->name . ' with requestData: ' . json_encode($request->all()));
        try {
            if (!$request->has('stage_type') || empty($request->input('stage_type'))) {
                return Redirect::back()->with(['message' => 'Please select stage type', 'alert-type' => 'error']);
            }
            if (!$request->has('stage_name') || empty($request->input('stage_name'))) {
                return Redirect::back()->with(['message' => 'Please enter stage name', 'alert-type' => 'error']);
            }

            $nextSequence = MasterProductStages::max('sequence') + 1;
            MasterProductStages::create([
                'sequence' => $nextSequence,
                'name' => $request->input('stage_name'),
                'type' => $request->input('stage_type'),
            ]);
            return Redirect::back()->with(['message' => 'Stage added successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('addStages: ' . $e->getMessage());
            return Redirect::back()->with(['message' => 'Failed to add stage', 'alert-type' => 'error']);
        }
    }

    #delete product's graphic types
    public function deleteStages($id)
    {
        Log::channel('daily')->info('deleteStages: Attempting to delete stages with ID ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $type = MasterProductStages::find($id);
            if (!$type) {
                return response()->json(['status' => false, 'message' => 'Stage not found', 'alert-type' => 'error']);
            }
            $type->delete();
            return response()->json(['status' => true, 'message' => 'Stage deleted successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteStages: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to delete stage', 'alert-type' => 'error']);
        }
    }

    #change sequence of stages
    public function changeSequence(Request $request)
    {
        Log::channel('daily')->info('changeSequence: Attempting to change stage sequence by the user ' . Auth::user()->name . ' With the Data: ' . json_encode($request->all()));
        try {
            if ($request->sequence) {
                $sequence = json_decode($request->sequence);
                foreach ($sequence as $key => $value) {
                    $client = MasterProductStages::findOrFail($key);
                    $client->sequence = $value;
                    $client->save();
                }
            }
            return response()->json(['alert-type' => 'success', 'message' => 'Stage sequence updated successfully']);
        } catch (\Throwable $e) {
            Log::channel('exception')->error('changeSequence: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Unable to update stage sequence', 'alert-type' => 'error']);
        }
    }
}
