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
     * Logs an API request both to the database (via Job) and to the log file.
     *
     * @param string  $endpoint   The actual URL path (e.g. /api/refresh)
     * @param string  $action     Logical action (e.g. refresh, retrieve, redirect)
     * @param Request $req        The incoming HTTP request
     * @param int     $status     HTTP response status code (e.g. 200, 422)
     * @param bool    $success    Whether the action succeeded (default: true)
     * @param array   $extra      Extra data to log (e.g. response info)
     */
    public static function fullLog(
        string $endpoint,
        string $action,
        Request $req,
        int $status,
        bool $success = true,
        array $extra = []
    ): void {
        $params = array_merge($req->all(), $extra);
        $userId = Auth::check() ? Auth::id() : null;

        Bus::dispatch(new LogApiRequest(
            endpoint: $endpoint,
            action: $action,
            method: $req->method(),
            ip: $req->ip(),
            params: $params,
            status: $status,
            success: $success,
            user_id: $userId
        ));

        Log::channel('mapping')->info("[$endpoint] $action API", $params);
    }
}
