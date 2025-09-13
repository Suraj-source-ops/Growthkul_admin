<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            $totalServices = Services::count();
            return view('dashboard.dashboard', [
                'services' => $totalServices ?? 0,
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('dashboard: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch the records', 'alert-type' => 'error']);
        }
    }
}
