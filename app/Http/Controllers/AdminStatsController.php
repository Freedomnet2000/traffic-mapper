<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestLog;
use Illuminate\Support\Facades\Auth;

class AdminStatsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role->value !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'success' => RequestLog::where('success', true)->count(),
            'failed'  => RequestLog::where('success', false)->count(),
            'by_action' => RequestLog::selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action'),
            'by_day' => RequestLog::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                ->groupByRaw('DATE(created_at)')
                ->orderBy('day', 'desc')
                ->limit(7)
                ->pluck('count', 'day')
                ->reverse(),

            'by_day_action' => RequestLog::selectRaw('DATE(created_at) as day, action, COUNT(*) as count')
                ->groupByRaw('DATE(created_at), action')
                ->orderByRaw('DATE(created_at) ASC')
                ->get()
                ->groupBy('day')
                ->map(function ($dayGroup) {
                    $result = ['date' => $dayGroup->first()->day];
                    foreach ($dayGroup as $row) {
                        $result[$row->action] = (int) $row->count;
                    }
                    return $result;
                })
                ->values(),

        ];

        return response()->json($stats);
    }

    /**
     * Get the last 50 failed requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failures()
    {
        $user = Auth::user();
        if (!$user || $user->role->value !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logs = \App\Models\RequestLog::where('success', false)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get([
                'endpoint', 'action', 'ip', 'params', 'status', 'created_at'
            ]);

        return response()->json($logs);
    }

}
