<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\ProductUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class HistoryController extends Controller
{
    #fetch notification
    public function fetchNotification()
    {
        try {
            return DataTables::of($notifications = [])
                ->editColumn('note', function ($notification) {
                    return view('components.notification-item', ['notification' => [$notification]])->render();
                })
                ->rawColumns(['note'])
                ->make(true);
        } catch (Exception $e) {
            Log::channel('exception')->error('fetchNotification: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch notification', 'alert-type' => 'error']);
        }
    }


    public function markAsRead(Request $request)
    {
        try {
            $notification = History::where('id', $request->id)->first();
            if ($notification) {
                $notification->status = !$notification->status;
                $notification->save();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false], 404);
        } catch (\Throwable $e) {
            Log::channel('exception')->error('fetchNotification: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return redirect()->back()->with(['message' => 'Failed to fetch notification', 'alert-type' => 'error']);
        }
    }
}
