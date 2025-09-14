<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Services;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function dashboard()
    {
        try {
            $totalServices = Services::count();
            $totalEnquiry = Enquiry::count();
            return view('dashboard.dashboard', [
                'services' => $totalServices ?? 0,
                'enquiries' => $totalEnquiry ?? 0,
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('dashboard: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch the records', 'alert-type' => 'error']);
        }
    }
}
