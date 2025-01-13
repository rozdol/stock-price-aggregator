<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock;

class StocksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = [
            ['symbol' => 'MSFT', 'name' => 'Microsoft Corporation'],
            ['symbol' => 'IBM', 'name' => 'IBM Corporation'],
            ['symbol' => 'AAPL', 'name' => 'Apple Inc.'],
            ['symbol' => 'TSLA', 'name' => 'Tesla Inc.'],
            ['symbol' => 'QQQ', 'name' => 'Invesco QQQ Trust'],
            ['symbol' => 'AMZN', 'name' => 'Amazon.com, Inc.'],
            ['symbol' => 'META', 'name' => 'Meta Platforms, Inc.'],
            ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc.'],
            ['symbol' => 'NVDA', 'name' => 'NVIDIA Corporation'],
            ['symbol' => 'CEG', 'name' => 'Constellation Energy Corporation'],
            ['symbol' => 'WBA', 'name' => 'Walgreens Boots Alliance Inc.'],
            ['symbol' => 'BA', 'name' => 'The Boeing Company'],
            ['symbol' => 'SPY', 'name' => 'SPDR S&P 500 ETF Trust'],
            ['symbol' => 'BRK-B', 'name' => 'Berkshire Hathaway Inc.'],
            ['symbol' => 'IWM', 'name' => 'iShares Russell 2000 ETF']
        ];

        foreach ($stocks as $stock) {
            Stock::updateOrCreate(
                ['symbol' => $stock['symbol']],
                ['name' => $stock['name']]
            );
        }
    }
}
