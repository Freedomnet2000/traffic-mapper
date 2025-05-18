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
            return response()->json(['error' => 'Missing track_id'], 422);
        }

        $log = RequestLog::where('track_id', $trackId)->latest()->first();

        if (!$log) {
            return response()->json(['error' => 'Invalid track_id'], 404);
        }
        // Uncomment the following lines if you want to save the pingback details
        // $log->pingback_received = true;
        // $log->pingback_ip = $request->ip();
        // $log->pingback_at = now();
        // $log->save();

        LogHelper::fullLog(
            endpoint: '/api/pingback',
            action: 'pingback',
            req: $request,
            status: 200,
            success: true,
            track_id: $trackId,
            extra: ['note' => 'Pingback confirmed']
        );

        return response()->json(['status' => 'ok']);
    }
}

