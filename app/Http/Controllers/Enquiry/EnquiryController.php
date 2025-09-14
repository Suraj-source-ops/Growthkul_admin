<?php

namespace App\Http\Controllers\Enquiry;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class EnquiryController extends Controller
{
    #call permissions
    public function __construct()
    {
        $this->middleware('permission:enquiry-lists-enquiries', ['only' => ['enquiryList']]);
    }
    #list enquiry
    public function enquiryList(Request $request)
    {
        try {
            if ($request->ajax()) {
                $enquirylist = Enquiry::select('*')->orderBy('id', 'desc');
                return DataTables::of($enquirylist)
                    ->editColumn('created_at', function ($row) {
                        return date('d-m-Y H:i A', strtotime($row->created_at));
                    })
                    ->editColumn('is_subscribed', function ($row) {
                        if ($row->is_subscribed == 1) {
                            return '<span class="badge badge-success py-2" style="color:#FFF;">Subscribed</span>';
                        } else {
                            return '<span class="badge badge-danger py-2" style="color:#FFF;">Not subscribed</span>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['is_subscribed'])
                    ->make(true);
            }
            return view('enquiry.enquiry');
        } catch (Exception $e) {
            Log::channel('exception')->error('enquiryList: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch enquiry', 'alert-type' => 'error']);
        }
    }
}
