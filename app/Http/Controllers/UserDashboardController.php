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
        $valves = Valve::all();
        $activeSchedules = Schedule::where('is_active', true)->get();
        $recentLogs = OperationLog::with('valve')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('valves', 'activeSchedules', 'recentLogs'));
    }
}
