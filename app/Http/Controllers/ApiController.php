<?php

namespace App\Http\Controllers;

use App\Models\Mapping;
use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use App\Services\MappingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ApiController extends Controller
{
    /**
     * Retrieve the original mapping values for the given our_param, only if it's the latest version.
     *
     * @param string $our_param
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieve(Request $request, ?string $our_param = null)
    {
        $request->merge(['our_param' => $our_param]);

        try {
            $validated = $request->validate([
                'our_param' => [
                    'required',
                    'string',
                    'min:2',
                    'max:255',
                    'regex:/^[A-Za-z0-9._-]+$/'
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

        $param = $validated['our_param'];
        Log::channel('mapping')->debug('Retrieve API called', ['our_param' => $param]);

        try {
            $map = Mapping::query()
                ->from('mappings as m')
                ->where('m.our_param', $param)
                ->whereRaw('m.version = (
                    select max(inner_m.version)
                    from mappings as inner_m
                    where inner_m.keyword  = m.keyword
                      and inner_m.src      = m.src
                      and inner_m.creative = m.creative
                )')
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            LogHelper::fullLog('/api/retrieve_original/' . $param, $request, 422, [
                'note' => 'Retrieve failed',
            ]);
            return response()->json([
                'message' => 'Not Found',
                'errors'  => ['our_param' => ['The provided our_param was not found.']],
            ], 422);
        }

        LogHelper::fullLog('retrieve_original/' , $request, 200, [
            'note' => 'Retrieve success',
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
        try {
            $data = $req->validate([
                'our_param' => [
                    'required',
                    'string',
                    'min:2',
                    'max:255',
                    'regex:/^[A-Za-z0-9._-]+$/'
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }
         Log::channel('mapping')->info('Refresh API called', ['our_param' => $data['our_param']]);
         
         try {
            $map = Mapping::where('our_param', $req->our_param)->firstOrFail();

        } catch (ModelNotFoundException $e) {
            LogHelper::fullLog('/api/refresh', $req, 422, [
                'note' => 'Refresh failed',
            ]);
            return response()->json([
                'message' => 'Not Found',
                'errors'  => ['our_param' => ['The provided our_param was not found.']],
            ], 422);
        }
         
         $new = $svc->refresh($map);

         LogHelper::fullLog('/api/refresh', $req, 200, [
             'note' => 'Refresh success',
            'new_param' => $new->our_param,
         ]);
         return response()->json(['new_param' => $new->our_param]);
     }
     
}
