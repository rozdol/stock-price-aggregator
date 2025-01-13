<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Stock;

class StockController extends Controller
{
    public function getSymblos()
    {
        $stock = Stock::all();
        if (!$stock) {
            return response()->json(['message' => 'No data found'], 404);
        }
        return response()->json($stock);
    }
}
