<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Trust all proxies (e.g. Render, Cloudflare, etc.)
     */
    protected $proxies = '*';

    /**
     * Trust common headers used by proxies/load balancers
     */
    protected $headers = 0b111111; // Equivalent to HEADER_X_FORWARDED_ALL
}
