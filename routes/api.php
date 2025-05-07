<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


Route::post('/refresh', [ApiController::class, 'refresh']);
Route::get('/retrieve_original/{our_param}', [ApiController::class, 'retrieve']);
