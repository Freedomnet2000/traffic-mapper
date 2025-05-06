<?php

namespace App\Http\Controllers;

use App\Models\Mapping;
use Illuminate\Http\Request;
use App\Services\MappingService;

class ApiController extends Controller
{
    public function retrieve(string $our_param)
    {
        $map = Mapping::where('our_param',$our_param)->firstOrFail();
        return response()->json($map->only('keyword','src','creative'));
    }
    
    public function refresh(Request $req, MappingService $svc)
    {
        $map = Mapping::where('our_param',$req->our_param)->firstOrFail();
        $new = $svc->refresh($map);
        return response()->json(['new_param'=>$new->our_param]);
    }
    
}
