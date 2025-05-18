<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use App\Services\MappingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


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
        try {
            $data = $req->validate([
                'keyword'  => [
                    'required',
                    'string',
                    'min:2',
                    'max:255',
                    'regex:/^[A-Za-z0-9._-]+$/'   // letters, digits, dot, underscore, hyphen
                ],
                'src'      => [
                    'required',
                    'string',
                    'min:2',
                    'max:255',
                    'regex:/^[A-Za-z0-9._-]+$/'
                ],
                'creative' => [
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
                req: $req,
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

        $keyword  = $data['keyword'];
        $src      = $data['src'];
        $creative = $data['creative'];

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

        LogHelper::fullLog(
                endpoint: '/redirect',
                action: 'redirect',
                req: $req,
                status: 302,
                success: true,
                extra: [
                    'note' => 'Redirect success',
                ]
        );

        return redirect()->away($redirectUrl, 302);
    }
}
