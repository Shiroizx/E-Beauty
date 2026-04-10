<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $roleCounts = User::query()
            ->selectRaw('role, count(*) as c')
            ->groupBy('role')
            ->pluck('c', 'role');

        $recentLogs = ActivityLog::query()
            ->with('actor')
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.super-dashboard', compact('roleCounts', 'recentLogs'));
    }
}
