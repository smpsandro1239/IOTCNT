<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $valves = Valve::orderBy('valve_number')->get();
        $activeSchedules = Schedule::where('is_enabled', true)->get();
        $recentLogs = OperationLog::with('valve')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('valves', 'activeSchedules', 'recentLogs'));
    }
}
