<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockPrice;
use Illuminate\Support\Facades\Artisan;

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
