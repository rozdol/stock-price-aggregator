<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PriceController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/chart/{symbol}', [PriceController::class, 'showChart'])->name('chart');
Route::get('/screener', [PriceController::class, 'showScreener'])->name('screener');;
Route::get('/screener/data', [PriceController::class, 'getScreenerData'])->name('screener.data');
