<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/stocks/{symbol}', [StockController::class, 'getLatestPrice']);

// Route::get('/stocks', [StockController::class, 'getSymblos']);
