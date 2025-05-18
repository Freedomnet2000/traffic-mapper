<?php

// app/Http/Controllers/PingbackController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestLog;

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

        $log->pingback_received = true;
        $log->pingback_ip = $request->ip();
        $log->pingback_at = now();
        $log->save();

        return response()->json(['status' => 'ok']);
    }
}

