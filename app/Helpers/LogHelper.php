<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Jobs\LogApiRequest;

class LogHelper
{
    /**
     * Logs an API request to both the log file and the database (via Job).
     *
     * @param string  $endpoint  The actual route/URI (e.g. /api/refresh)
     * @param string  $action    Logical name of the action (e.g. refresh, retrieve)
     * @param Request $req       The incoming HTTP request
     * @param int     $status    HTTP response status code (e.g. 200, 422)
     * @param bool    $success   Whether the action succeeded
     * @param array   $extra     Any extra fields to include (optional)
     * @param string|null $track_id Optional tracking ID for the request
     */
    public static function fullLog(
        string $endpoint,
        string $action,
        Request $req,
        int $status,
        bool $success = true,
        ?string $track_id = null,
        array $extra = []
    ): void {
        $userId = Auth::check() ? Auth::id() : null;

        // Safely extract the real IP address
        $ip = $req->header('x-forwarded-for')
            ? trim(explode(',', $req->header('x-forwarded-for'))[0])
            : $req->ip();

        $params = array_merge($req->all(), $extra);

        // Queue database logging
        Bus::dispatch(new LogApiRequest(
            endpoint: $endpoint,
            action: $action,
            method: $req->method(),
            ip: $ip,
            params: $params,
            status: $status,
            success: $success,
            track_id: $track_id,
            user_id: $userId
        ));

        // File logging
        Log::channel('mapping')->info("[$endpoint] $action API", [
            'ip' => $ip,
            'user_id' => $userId,
            'status' => $status,
            'success' => $success,
            'params' => $params,
            'track_id' => $track_id,
        ]);
    }
}
