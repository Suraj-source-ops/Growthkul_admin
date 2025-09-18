<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Services;
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
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:delete-service-button-services|add-service-button-services|service-lists-services', ['only' => ['serviceList']]);
        $this->middleware('permission:add-service-button-services', ['only' => ['addServiceName']]);
        $this->middleware('permission:delete-service-button-servicess', ['only' => ['deleteServiceName']]);
    }

    #services add and list view
    public function serviceList(Request $request)
    {
        try {
            if ($request->ajax()) {
                $serviceList = Services::select('*')->orderBy('id', 'desc');
                return DataTables::of($serviceList)
                    ->addIndexColumn()
                    ->editColumn('action', function ($row) {
                        return '<div class="">
                            <a href="javascript:void(0);" onclick="deleteServiceName(' . $row->id . ')" class="text-danger" title="Delete">
                                <i class="far fa-trash-alt tras-icons"></i>
                            </a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('settings.service.service-types');
        } catch (Exception $e) {
            Log::channel('exception')->error('serviceList: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch service types', 'alert-type' => 'error']);
        }
    }

    #add addServiceName
    public function addServiceName(Request $request)
    {
        try {
            if (!$request->has('name') || empty($request->input('name'))) {
                return Redirect::back()->with(['message' => 'Please enter service name', 'alert-type' => 'error']);
            }

            Services::create([
                'name' => $request->input('name'),
            ]);
            return Redirect::back()->with(['message' => "Service type added successfully", 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('addServiceName: ' . $e->getMessage());
            return Redirect::back()->with(['message' => 'Failed to add service name', 'alert-type' => 'error']);
        }
    }

    #delete service type
    public function deleteServiceName($id)
    {
        Log::channel('daily')->info('deleteServiceName: Attempting to delete service name of ID ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $type = Services::find($id);
            if (!$type) {
                return response()->json(['status' => false, 'message' => 'Service name not found', 'alert-type' => 'error']);
            }
            $type->delete();
            return response()->json(['status' => true, 'message' => 'Service name deleted successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteServiceName: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to delete service name', 'alert-type' => 'error']);
        }
    }
}
