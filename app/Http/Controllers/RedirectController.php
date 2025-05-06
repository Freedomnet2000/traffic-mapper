<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MappingService;

class RedirectController extends Controller
{
    public function handle(Request $req, MappingService $svc)
        {
            $map = $svc->getOrCreate(
                $req->query('keyword'),
                $req->query('src'),
                $req->query('creative')
            );

            $affiliate = config('app.affiliate_url', env('AFFILIATE_URL'));
            $url = $affiliate.'?our_param='.$map->our_param;

            return redirect()->away($url,302);
        }

}
