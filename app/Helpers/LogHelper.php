<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use App\Jobs\LogApiRequest;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function fullLog(string $endpoint, Request $req, int $status, array $extra = []): void
    {
        $params = array_merge($req->all(), $extra);

        Bus::dispatch(new LogApiRequest(
            endpoint: $endpoint,
            method: $req->method(),
            ip: $req->ip(),
            params: $params,
            status: $status
        ));

        Log::channel('mapping')->info("[$endpoint] API Hit", array_merge($req->all(), $extra));
    }
}
   