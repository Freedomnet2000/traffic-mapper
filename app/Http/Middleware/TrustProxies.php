<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;


class TrustProxies extends Middleware
{
    /**
     * Trust all proxies (e.g. Render, Cloudflare, etc.)
     */
    protected $proxies = '*';

    /**
     * Trust headers needed to determine original client IP, host, port, and protocol
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
