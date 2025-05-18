<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PingbackController;
use App\Http\Controllers\AdminStatsController;


Route::post('/refresh', [ApiController::class, 'refresh']);
Route::get('/retrieve_original/{our_param?}', [ApiController::class, 'retrieve']);
Route::middleware(['auth', 'verified'])->get('/admin/stats', [AdminStatsController::class, 'index']);
Route::post('/pingback', [PingbackController::class, 'handle']);

