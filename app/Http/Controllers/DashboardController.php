<?php

namespace App\Http\Controllers;

use App\Models\Mapping;
use App\Models\RequestLog;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $mappings = $user->role->value === 'admin'
            ? Mapping::orderBy('created_at', 'desc')->paginate(20)
            : null;

        $stats = $user->role->value === 'admin'
            ? [
                'success' => RequestLog::where('success', true)
                    ->where('pingback_received', true)
                    ->count(),
                'failed' => RequestLog::where('success', false)
                    ->where('action', 'redirect')
                    ->count(),
                'pending' => RequestLog::where('success', true)
                    ->where(function ($q) {
                        $q->whereNull('pingback_received')->orWhere('pingback_received', false);
                    })->count(),
                'by_action' => RequestLog::selectRaw('action, COUNT(*) as count')
                    ->groupBy('action')
                    ->pluck('count', 'action'),
            ]
            : [];

        return Inertia::render('Dashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
            'mappings' => $mappings,
            'stats' => $stats,
        ]);
    }
}
