<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PriceController;


Route::get('/', function () {
    return redirect('/screener');
});

Route::get('/chart/{symbol}', [PriceController::class, 'showChart'])->name('chart'); // Display price chart of the symbol
Route::get('/screener', [PriceController::class, 'showScreener'])->name('screener'); // Dispaly the screener report
Route::get('/screener/data', [PriceController::class, 'getScreenerData'])->name('screener.data'); // get fresh table of prices
