<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PriceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('report', [PriceController::class, 'makeReport']); // Get JSON report
Route::get('prices/fetch', [PriceController::class, 'fetchPrice']); // Invoke the command to fetch the prices from alphavantage
Route::get('prices/{symbol}', [PriceController::class, 'getLatestPrice']); // Get JSON with latest price of the symbol
Route::get('history/{symbol}', [PriceController::class, 'getHistory']); // Get JSON with historical prices of the symbol
Route::apiResource('stocks', StockController::class);  // Get JSON list of available symbols
Route::apiResource('prices', PriceController::class); // Get JSON raw price data
