<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequestLog;
use Illuminate\Http\Request;

class PendingController extends Controller
{
    public function index(Request $request)
    {
        $logs = RequestLog::query()
            ->where('success', true)
            ->where(function ($q) {
                $q->whereNull('pingback_received')->orWhere('pingback_received', false);
            })
            ->orderByDesc('created_at')
            ->limit(100)
            ->get([
                'track_id',
                'endpoint',
                'ip',
                'status',
                'params',
                'created_at',
            ]);

        return response()->json($logs);
    }
}
