<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use Illuminate\Console\Scheduling\AsScheduled;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// #[AsScheduled('everyMinute')] 

class FetchStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:stock-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch real-time stock prices and store them in the database.';


    /**
     * Save data in database and in cache
     */
    private function storePrice($symbol, $price)
    {
        $stockId = Stock::where('symbol', $symbol)->value('id');
        if ($stockId) {
            // Store in DB
            StockPrice::create([
                'stock_id' => $stockId,
                'price' => $price,
                'retrieved_at' => now(),
            ]);

            // Store data in Cache
            $data = Cache::get("stock_bkp:{$symbol}");
            if ($data) {
                // dump($data);
                $previousPrice = $data['price'];
                $change_pct = (($price - $previousPrice) / $previousPrice) * 100;
                $retrieved_at_prev = Carbon::parse($data['retrieved_at']);
                $secondsDifference = $retrieved_at_prev->diffInSeconds(now());
            } else {
                $change_pct = 0;
                $previousPrice = 0;
                $secondsDifference = 0;
            }
            // Update cache
            $cached_data = [
                'price' => $price,
                'change_pct' => $change_pct,
                'retrieved_at' => now()->toDateTimeString(),
            ];
            Log::info("{$symbol} price:$price and $secondsDifference seconds from previous price=$previousPrice");

            // dump($cached_data);

            Cache::put("stock:{$symbol}", $cached_data, 60); // keep only 60 seconds fresh prices
            Cache::put("stock_bkp:{$symbol}", $cached_data, 3600); // keep data tocalculate change_pct of the refresh period is more than 60 seconds.
            $this->info("Updated cache for $symbol at price=$price with change_pct=$change_pct");
        } else {
            $this->error("No $symbol in the database.");
        }
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {

        /**
         * We can utilize the API endpoint to fetch multiple symbols in one request if we have
         * a premium subscription. Otherwise, we fetch prices for each symbol per request.
         * 
         * The command uses try-catch construction to implement error handling.
         * Also we use Http::retry to improve sustainability.
         * 
         * We use the main cache to hold only fresh data, and a backup cache to get previous prices for change calculations
         * 
         */


        $stocks = Stock::all();
        $apiKey = env("ALPHA_VANTAGE_API_KEY", "demo");
        $isPremium = env("ALPHA_VANTAGE_PEMIUM", false);
        $is_delayed = env("ALPHA_VANTAGE_DELAYED", true);;

        $delyed_str = $is_delayed ? 'delayed' : '';
        $this->info("delyed_str: $delyed_str");

        // Get prices for all symbols in one query if the the account is premium
        if ($isPremium) {
            Log::info("Premium");
            $this->info("Premium");
            $symbols = Stock::pluck('symbol')->toArray();
            $commaSeparatedSymbols = implode(',', $symbols);
            try {
                $response = Http::retry(3, 1000) // Retry up to 3 times with 1-second intervals
                    ->timeout(5) // Set a timeout of 5 seconds
                    ->get("https://www.alphavantage.co/query", [
                        'function' => 'REALTIME_BULK_QUOTES',
                        'symbol' => $commaSeparatedSymbols,
                        'apikey' => $apiKey,
                    ]);
                if ($response->ok()) {
                    // print_r($commaSeparatedSymbols);
                    $prices = $response->json()['data'] ?? null;
                    if ($prices) {
                        // dd($response->json()['data']);
                        foreach ($prices as $item) {
                            $symbol = $item['symbol'];
                            $price = (float) $item['close'];

                            // Store in DB
                            $this->storePrice($symbol, $price);

                            echo ("Symbol: $symbol \n");
                            $data = $response->json()['Global Quote'] ?? null;
                        }
                    } else {
                        $this->error("Failed to fetch data for stock: {$commaSeparatedSymbols} (HTTP Status: {$response->status()})");
                    }
                } else {
                    $this->info('No data for  ' . $commaSeparatedSymbols);
                }
            } catch (\Exception $e) {
                // Catch exceptions (e.g., connection issues, timeouts)
                $this->error("Error fetching data for stock: {$commaSeparatedSymbols} - {$e->getMessage()}");
                Log::error("Error fetching data for stock: {$commaSeparatedSymbols}", ['exception' => $e]);
            }
        } else {
            // Get prices for symbols one by one if the the account is not premium
            Log::info("NOT Premium");
            $this->info("NOT Premium");
            foreach ($stocks as $stock) {
                try {

                    $response = Http::retry(3, 1000) // Retry up to 3 times with 1-second intervals
                        ->timeout(5) // Set a timeout of 5 seconds
                        ->get("https://www.alphavantage.co/query", [
                            'function' => 'GLOBAL_QUOTE',
                            'symbol' => $stock->symbol,
                            'entitlement' => $delyed_str,
                            'apikey' => $apiKey,
                        ]);

                    // dd($response->json());
                    $this->info('Quering ' . $stock->symbol);

                    if ($response->ok()) {
                        // dd($response);
                        if ($is_delayed) {
                            $data = $response->json()['Global Quote - DATA DELAYED BY 15 MINUTES'] ?? null;
                        } else {
                            $data = $response->json()['Global Quote'] ?? null;
                        }

                        // dd($data);
                        if ($data) {
                            // dd($data);
                            $price = (float) $data['05. price'];
                            $this->info($stock->symbol . ': ' . $price);

                            // Store in DB
                            $this->storePrice($stock->symbol, $price);


                            // 

                        } else {
                            Log::error("Failed to fetch data for stock: {$stock->symbol} (HTTP Status: {$response->status()})");
                        }
                    } else {
                        $this->info('No data for  ' . $stock->symbol);
                    }
                } catch (\Exception $e) {
                    // Catch exceptions (e.g., connection issues, timeouts)
                    $this->error("Error fetching data for stock: {$stock->symbol} - {$e->getMessage()}");
                    Log::error("Error fetching data for stock: {$stock->symbol}", ['exception' => $e]);
                }
            }

            $this->info('Stock prices updated successfully.');
            Log::info('Stock prices updated successfully.');
        }
    }
}
