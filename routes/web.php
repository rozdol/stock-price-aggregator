<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PriceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chart/{symbol}', [PriceController::class, 'showChart']);
Route::get('/screener', [PriceController::class, 'showScreener']);
