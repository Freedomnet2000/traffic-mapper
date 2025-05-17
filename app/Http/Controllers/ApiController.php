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
            LogHelper::fullLog(
                endpoint: '/redirect',
                action: 'redirect',
                req: $request,
                status: 422,
                success: false,
                extra: [
                    'note' => 'Validation failed',
                    'errors' => $e->errors(),
                ]
            );
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
            LogHelper::fullLog(
                endpoint: '/api/retrieve_original/' . $param,
                action: 'retrieve',
                req: $request,
                status: 422,
                success: false,
                extra: [
                    'note' => 'Retrieve failed',
                    'errors' => ['our_param' => ['The provided our_param was not found.']],
                ]
             );
            return response()->json([
                'message' => 'Not Found',
                'errors'  => ['our_param' => ['The provided our_param was not found.']],
            ], 422);
        }
            LogHelper::fullLog(
                endpoint: '/api/retrieve_original/' . $param,
                action: 'retrieve',
                req: $request,
                status: 200,
                success: true,
                extra: [
                    'note' => 'Retrieve success',
                ]
             );
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
            LogHelper::fullLog(
                endpoint: '/api/refresh',
                action: 'refresh',
                req: $req,
                status: 422,
                success: false,
                extra: [
                    'note' => 'Validation failed',
                    'errors' => $e->errors(),
                ]
            );
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }
         Log::channel('mapping')->info('Refresh API called', ['our_param' => $data['our_param']]);
         
         try {
            $map = Mapping::where('our_param', $req->our_param)->firstOrFail();

        } catch (ModelNotFoundException $e) {
            LogHelper::fullLog(
                endpoint: '/api/refresh',
                action: 'refresh',
                req: $req,
                status: 422,
                success: false,
                extra: [
                    'note' => 'Refresh keyword failed',
                    'errors' => ['our_param' => ['The provided our_param was not found.']],
                ]
          );
            return response()->json([
                'message' => 'Not Found',
                'errors'  => ['our_param' => ['The provided our_param was not found.']],
            ], 422);
        }
         
         $new = $svc->refresh($map);

        LogHelper::fullLog(
            endpoint: '/api/refresh',
            action: 'refresh',
            req: $req,
            status: 200,
            success: true,
            extra: [
                'note' => 'Refresh success',
                'new_param' => $new->our_param,
            ]
        );
         return response()->json(['new_param' => $new->our_param]);
     }
     
}
