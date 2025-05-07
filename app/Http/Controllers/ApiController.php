<?php

namespace App\Http\Controllers;

use App\Models\Mapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MappingService;

class ApiController extends Controller
{
    /**
     * Retrieve the original mapping values for the given our_param, only if it's the latest version.
     *
     * @param string $our_param
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieve(string $our_param)
    {
        Log::channel('mapping')->debug('Retrieve API called', ['our_param' => $our_param]);

        $map = Mapping::query()
            ->from('mappings as m')
            ->where('m.our_param', $our_param)
            ->whereRaw('m.version = (
                select max(inner_m.version)
                from mappings as inner_m
                where inner_m.keyword  = m.keyword
                  and inner_m.src      = m.src
                  and inner_m.creative = m.creative
            )')
            ->firstOrFail();

        Log::channel('mapping')->debug('Retrieve API success', [
            'keyword'  => $map->keyword,
            'src'      => $map->src,
            'creative' => $map->creative,
        ]);

        return response()->json($map->only('keyword', 'src', 'creative'));
    }

    /**
     * Refresh an existing mapping to generate a new our_param.
     *
     * @param Request $req
     * @param MappingService $svc
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $req, MappingService $svc)
    {
        $ourParam = $req->input('our_param');
        Log::channel('mapping')->debug('Refresh API called', ['our_param' => $ourParam]);

        $map = Mapping::where('our_param', $ourParam)->firstOrFail();
        $new = $svc->refresh($map);

        Log::channel('mapping')->info('Refresh API success', [
            'old_param' => $ourParam,
            'new_param' => $new->our_param,
            'new_version' => $new->version,
        ]);

        return response()->json(['new_param' => $new->our_param]);
    }
}
