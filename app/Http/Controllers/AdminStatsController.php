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
                ->reverse() // כדי שהימים יהיו מהישן לחדש
        ];

        return response()->json($stats);
    }
}
