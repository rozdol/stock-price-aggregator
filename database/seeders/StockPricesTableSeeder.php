<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Support\Carbon;

class StockPricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Stock::all();
        $currentTime = Carbon::now();
        foreach ($stocks as $stock) {
            for ($i = 0; $i < 60; $i++) {
                $retrievedAt = $currentTime->copy()->subMinutes($i);

                StockPrice::create([
                    'stock_id' => $stock->id,
                    'price' => $this->generateRandomPrice(100, 500),
                    'retrieved_at' => $retrievedAt,
                ]);
            }
        }
    }

    private function generateRandomPrice($min, $max)
    {
        return round(mt_rand($min * 100, $max * 100) / 100, 2); // Round to 2 decimal places
    }
}
