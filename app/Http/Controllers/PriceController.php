<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockPrice;
use App\Models\Stock;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

    public function getLatestPrice($symbol)
    {
        // Try to get stock data from cache
        $data = Cache::get("stock:{$symbol}");
        if (!$data) {
            // Fallback: Query the database for the latest stock price
            $stock = Stock::where('symbol', $symbol)->first();

            if (!$stock) {
                return response()->json(['message' => 'Stock not found.'], 404);
            }

            $latestPrice = $stock->prices()->latest('retrieved_at')->first();

            if (!$latestPrice) {
                return response()->json(['message' => 'Stock price data not available.'], 404);
            }

            // Format data
            $data = [
                'price' => $latestPrice->price,
                'retrieved_at' => $latestPrice->retrieved_at->toDateTimeString(),
            ];

            // Store the data in cache for 1 minute
            Cache::put("stock:{$symbol}", $data, 60);
        }

        return response()->json($data);
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
            $change_pct = null; // Default if there's no previous price

            if ($previousPrice !== null) {
                $change_pct = (($price->price - $previousPrice) / $previousPrice) * 100;
            }

            $history[] = [
                'symbol' => $symbol,
                'price' => $price->price,
                'change_pct' => $change_pct !== null ? round($change_pct, 2) : null, // Round to 2 decimals
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
    public function makeReportFromDB()
    {
        $stocks = Stock::with(['prices' => function ($query) {
            $query->latest('retrieved_at')->limit(2);
        }])->get();


        // return $stocks;
        // Format the response
        $report = [];

        foreach ($stocks as $item) {
            // print_r($item);
            $currentPrice = $item->prices[0]->price;
            $previousPrice = $item->prices[1]->price;
            $change_pct = (($currentPrice - $previousPrice) / $previousPrice) * 100;
            $report[] = [
                'symbol'  => $item->symbol,
                'name'  => $item->name,
                'price' => $currentPrice,
                'price_prev' => $previousPrice,
                'change_pct' => $change_pct
            ];
        }
        return $report;

        $report = $stocks->map(function ($stock) {
            return [
                'symbol' => $stock->symbol,
                'name' => $stock->name,
                'latest_prices' => $stock->prices->map(function ($price) {
                    return [
                        'price' => $price->price,
                        'retrieved_at' => $price->retrieved_at->toDateTimeString(),
                    ];
                }),
            ];
        });

        return response()->json($report);
    }

    public function makeReport()
    {
        $stocks = Stock::all();

        $report = [];
        foreach ($stocks as $stock) {
            $data = Cache::get("stock:{$stock->symbol}");
            // dump($data);
            if ($data) {
                $report[] = [
                    'symbol'  => $stock->symbol,
                    'name'  => $stock->name,
                    'price' => $data['price'],
                    'change_pct' => $data['change_pct']
                ];
            }


            // $data = Cache::get("stock:{$symbol}");
        }
        return $report;
    }

    public function showChart($symbol)
    {
        // Find the stock by symbol
        $stock = Stock::where('symbol', $symbol)->first();

        if (!$stock) {
            return abort(404, 'Stock not found');
        }

        // Fetch prices for the stock
        $prices = $stock->prices()
            ->orderBy('retrieved_at', 'asc')
            // ->limit(60)
            ->get(['price', 'retrieved_at'])
            ->map(function ($price) {
                return [
                    'price' => $price->price,
                    'retrieved_at' => $price->retrieved_at->toDateTimeString(),
                ];
            });

        // Pass data to the view
        return view('chart', [
            'symbol' => $symbol,
            'prices' => $prices,
        ]);
    }
}
