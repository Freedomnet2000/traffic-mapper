<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MappingService;

class RedirectController extends Controller
{
    /**
     * Handle incoming redirect requests, generate or retrieve mapping, and redirect.
     *
     * @param Request $req
     * @param MappingService $svc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $req, MappingService $svc)
    {
        $keyword  = $req->query('keyword');
        $src      = $req->query('src');
        $creative = $req->query('creative');

        // Log incoming redirect request
        Log::channel('mapping')->info('Redirect request received', [
            'keyword'  => $keyword,
            'src'      => $src,
            'creative' => $creative,
        ]);

        // Retrieve or create mapping
        $map = $svc->getOrCreate($keyword, $src, $creative);

        // Log mapping details
        Log::channel('mapping')->debug('Mapping retrieved/created', [
            'id'        => $map->id,
            'our_param' => $map->our_param,
            'version'   => $map->version,
        ]);

        $affiliate = config('app.affiliate_url', env('AFFILIATE_URL'));
        $redirectUrl = $affiliate . '?our_param=' . $map->our_param;
        Log::channel('mapping')->info('Redirecting to affiliate URL', [
            'url' => $redirectUrl,
        ]);

        return redirect()->away($redirectUrl, 302);
    }
}
