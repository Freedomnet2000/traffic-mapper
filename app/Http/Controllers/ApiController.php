<?php

namespace App\Http\Controllers;

use App\Models\Mapping;
use Illuminate\Http\Request;
use App\Services\MappingService;

class ApiController extends Controller
{
    public function retrieve(string $our_param)
    {
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
    
        return response()->json($map->only('keyword', 'src', 'creative'));
    }
    
    
    public function refresh(Request $req, MappingService $svc)
    {
        $map = Mapping::where('our_param',$req->our_param)->firstOrFail();
        $new = $svc->refresh($map);
        return response()->json(['new_param'=>$new->our_param]);
    }
    
}
