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
    #History lists datatable
    public function productHistory(Request $request)
    {
        try {
            if ($request->ajax()) {
                $history = History::with(['user'])->where(['product_id' => $request->productId])
                    ->where(function ($query) {
                        $query->whereNull('assign_to')
                            ->orWhere('assign_to', '');
                    })
                    ->orderBy('id', 'desc');
                return DataTables::of($history)
                    ->addIndexColumn()
                    ->editColumn('name', function ($row) {
                        return $row->user->name ?? 'NA';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d-M-Y h:i A');
                    })
                    ->make(true);
            }
            return view('products.viewpartials.commentAndHistory.history.history');
        } catch (Exception $e) {
            Log::channel('exception')->error('productHistory: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch product history', 'alert-type' => 'error']);
        }
    }

    #Task History
    public function taskProductHistory(Request $request)
    {
        try {
            if ($request->ajax()) {
                $history = History::with(['user'])->where(['product_id' => $request->productId])
                    ->where(function ($query) {
                        $query->whereNull('assign_to')
                            ->orWhere('assign_to', '');
                    })
                    ->orderBy('id', 'desc');
                return DataTables::of($history)
                    ->addIndexColumn()
                    ->editColumn('name', function ($row) {
                        return $row->user->name ?? 'NA';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d-M-Y h:i A');
                    })
                    ->make(true);
            }
            return view('tasks.viewpartials.commentAndHistory.history.history');
        } catch (Exception $e) {
            Log::channel('exception')->error('productHistory: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch product history', 'alert-type' => 'error']);
        }
    }

    #fetch notification
    public function fetchNotification()
    {
        try {
            $assignedProduct = ProductUser::where('user_id', Auth::user()->id)
                ->pluck('product_id')
                ->toArray();

            $notifications = History::where(function ($query) use ($assignedProduct) {
                $query->whereIn('product_id', $assignedProduct);
            })->orWhere(function ($query) {
                $query->where('assign_to', Auth::user()->id);
            })->latest();

            return DataTables::of($notifications)
                ->editColumn('note', function ($notification) {
                    return view('components.notification-item', ['notification' => $notification])->render();
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
