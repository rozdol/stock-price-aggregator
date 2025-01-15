<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
// use App\Models\Stock;
// use App\Models\StockPrice;
// use Illuminate\Support\Facades\Http;

class StockTest extends TestCase
{
    public function test_api_price_data()
    {
        $response = $this->getJson('/api/prices/MSFT');

        // Assert the response is OK
        $response->assertStatus(200);

        // Assert the response contains a 'price' field
        $response->assertJsonStructure(['price']);

        // Assert the 'price' is positive
        $this->assertGreaterThan(0, $response->json('price'));
    }

    public function test_fetch_stock_prices_command_runs_successfully()
    {


        // Call the command
        $exitCode = Artisan::call('fetch:stock-prices');

        // Assert that the command runs successfully
        $this->assertEquals(0, $exitCode, 'Artisan command did not execute successfully.');

        // Assert that the recent price is in cache
        $this->assertTrue(Cache::has('stock:MSFT'));

        echo "Waiting 60 seconds for cache expiration...\n";

        sleep(60);
        // Assert that the value is no longer in the cache
        $this->assertFalse(Cache::has('stock:MSFT'));
    }
}
