<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PriceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('prices/fetch', [PriceController::class, 'fetchPrice']);
Route::get('prices/{symbol}', [PriceController::class, 'getLatestPrice']);
Route::get('history/{symbol}', [PriceController::class, 'getHistory']);
Route::apiResource('stocks', StockController::class);
Route::apiResource('prices', PriceController::class);
