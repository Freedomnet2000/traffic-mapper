<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RedirectController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/redirect', [RedirectController::class, 'handle']);     // GET /api/redirect?keyword=...
Route::get('/retrieve_original/{our_param}', [ApiController::class, 'retrieve']);
Route::post('/refresh', [ApiController::class, 'refresh']);