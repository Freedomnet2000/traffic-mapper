<?php

// app/Http/Controllers/PingbackController.php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\RequestLog;
use Illuminate\Http\Request;

class PingbackController extends Controller
{
    public function handle(Request $request)
    {
        $trackId = $request->input('track_id');

        if (!$trackId) {
            LogHelper::fullLog(
                endpoint: '/api/pingback',
                action: 'pingback',
                req: $request,
                status: 404,
                success: true,
                extra: ['note' => 'Pingback failed', 'error' => 'Missing track_id']
             );
            return response()->json(['error' => 'Missing track_id'], 422);
        }

        $log = RequestLog::where('track_id', $trackId)->latest()->first();

        if (!$log) {
            return response()->json(['error' => 'Invalid track_id'], 404);
        }
        $log->pingback_received = true;
        $log->pingback_ip = $request->ip();
        $log->pingback_at = now();
        $log->save();

        $track_id_log = 'track_id: ' . $log->track_id;
        LogHelper::fullLog(
            endpoint: '/api/pingback',
            action: 'pingback',
            req: $request,
            status: 200,
            success: true,
            extra: ['note' => 'Pingback confirmed', 'track_id_log' => $track_id_log]
        );

        return response()->json(['status' => 'ok']);
    }
}

