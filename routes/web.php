<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RedirectController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/redirect', [RedirectController::class, 'handle']); 

Route::get('/mock-affiliate', function (Request $req) {
    return response()->json([
        'received_param' => $req->query('our_param'),
        'message'        => 'Affiliate mock OK'
    ]);
});
Route::get('/admin', function () {
    return view('admin');
});
