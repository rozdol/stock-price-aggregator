<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockPrice;
use App\Models\Stock;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = StockPrice::get();
        return response()->json($stocks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getHistory($symbol)
    {
        $stock = Stock::where('symbol', $symbol)->first();
        if (!$stock) {
            return response()->json(['message' => 'Stock not found.'], 404);
        }
        $prices = $stock->prices()
            ->where('retrieved_at', '>=', Carbon::now()->subMinutes(60))
            ->orderBy('retrieved_at', 'desc')
            ->get(['price', 'retrieved_at']);

        if ($prices->isEmpty()) {
            return response()->json(['message' => 'No price data available for the last 60 minutes.'], 404);
        }

        $history = [];
        $previousPrice = null;

        foreach ($prices as $price) {
            $returns = null; // Default if there's no previous price

            if ($previousPrice !== null) {
                $returns = (($price->price - $previousPrice) / $previousPrice) * 100;
            }

            $history[] = [
                'symbol' => $symbol,
                'price' => $price->price,
                'returns' => $returns !== null ? round($returns, 2) : null, // Round to 2 decimals
                'retrieved_at' => $price->retrieved_at->toDateTimeString(),
            ];

            $previousPrice = $price->price; // Update previous price
        }

        return response()->json($history);
    }
    public function fetchPrice()
    {
        try {
            // Call the Artisan command
            Artisan::call('fetch:stock-prices');

            // Optionally, capture the output of the command
            $output = Artisan::output();

            // Return success response with the command's output
            return response()->json([
                'success' => true,
                'message' => 'Stock prices fetched successfully.',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            // Handle errors and return an appropriate response
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stock prices.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
