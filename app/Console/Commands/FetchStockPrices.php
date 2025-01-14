<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use Illuminate\Console\Scheduling\AsScheduled;
use Illuminate\Support\Facades\Log;

#[AsScheduled('everyMinute')]

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
     * Execute the console command.
     */
    public function handle()
    {
        $stocks = Stock::all();
        $apiKey = env("ALPHA_VANTAGE_API_KEY", "demo");
        // dd($apiKey);

        foreach ($stocks as $stock) {
            $response = Http::get("https://www.alphavantage.co/query", [
                'function' => 'GLOBAL_QUOTE',
                'symbol' => $stock->symbol,
                'apikey' => $apiKey,
            ]);

            $this->info('Quering ' . $stock->symbol);

            if ($response->ok()) {
                // dd($response);
                $data = $response->json()['Global Quote'] ?? null;
                // dd($data);
                if ($data) {
                    // dd($data);
                    $price = (float) $data['05. price'];
                    $this->info($stock->symbol . ': ' . $price);

                    // Store in DB
                    StockPrice::create([
                        'stock_id' => $stock->id,
                        'price' => $price,
                        'retrieved_at' => now(),
                    ]);

                    // 
                    $data = Cache::get("stock:{$stock->symbol}");
                    if ($data) {
                        // dump($data);
                        $previousPrice = $data['price'];
                        $change_pct = (($price - $previousPrice) / $previousPrice) * 100;
                    } else {
                        $change_pct = 0;
                    }
                    // Update cache
                    Cache::put("stock:{$stock->symbol}", [
                        'price' => $price,
                        'change_pct' => $change_pct,
                        'retrieved_at' => now()->toDateTimeString(),
                    ], 160);
                    $this->info("Updated cache for $stock->symbol at price=$price with change_pct=$change_pct");
                } else {
                    $this->error('ERROR. Chaeck API KEY');
                }
            } else {
                $this->info('No data for  ' . $stock->symbol);
            }
        }

        $this->info('Stock prices updated successfully.');
        Log::info('Stock prices updated successfully.');
    }
}
