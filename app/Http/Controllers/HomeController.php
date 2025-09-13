<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:view-dashboard-sidebar', ['only' => ['dashboard']]);
    }
    public function dashboard(Request $request)
    {
        try {
            $start = $request->start_date;
            $end = $request->end_date;
            $filter = $request->filter ?? null;
            $query = Product::query();
            if ($start && $end) {
                $query->whereBetween('created_at', [
                    Carbon::parse($start)->startOfDay(),
                    Carbon::parse($end)->endOfDay()
                ]);
            } else {
                switch ($filter) {
                    case 'Today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'This Week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'This Month':
                        $query->whereMonth('created_at', now()->month);
                        break;
                    case 'Past 3 Months':
                        $query->whereBetween('created_at', [now()->subMonths(3)->startOfMonth(), now()]);
                        break;
                }
            }
            $totalClients = Client::count();
            $totalProducts = $query->count();
            $inProgressCount = (clone $query)->where('product_status', 1)->count();
            $onHoldCount = (clone $query)->where('product_status', 2)->count();
            $completedCount = (clone $query)->where('product_status', 3)->count();
            return view('dashboard.dashboard', [
                'clients' => $totalClients ?? 0,
                'products' => $totalProducts ?? 0,
                'inProgressCount' => $inProgressCount,
                'onHoldCount' => $onHoldCount,
                'completedCount' => $completedCount,
                'selectedFilter' => $filter ?? null,
                'filterOptions' => ['Today', 'This Week', 'This Month', 'Past 3 Months']
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('dashboard: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch the records', 'alert-type' => 'error']);
        }
    }
}
