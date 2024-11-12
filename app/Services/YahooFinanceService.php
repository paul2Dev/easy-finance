<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YahooFinanceService
{
    protected $baseUrl = 'https://yahoo-finance15.p.rapidapi.com/api/v1/markets/stock/quotes';
    protected $rapidApiHost;
    protected $rapidApiKey;

    public function __construct()
    {
        // You can store your RapidAPI key in the .env file
        $this->rapidApiHost = env('YAHOO_FINANCE_RAPIDAPI_HOST');
        $this->rapidApiKey = env('YAHOO_FINANCE_RAPIDAPI_KEY');
    }

    /**
     * Fetch the actual price for a ticker symbol.
     *
     * @param string $ticker
     * @return float|null
     */
    public function getPrice(string $ticker): ?float
    {
        // Prepare the full URL
        $url = $this->baseUrl;

        // Make the API request to Yahoo Finance via RapidAPI
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => $this->rapidApiHost,
                'X-RapidAPI-Key' => $this->rapidApiKey
            ])->get($url, [
                'range' => '1d',   // You can adjust the range here if needed
                'interval' => '1m', // Set interval for fetching market data
                'ticker' => $ticker
            ]);

            // If the response is successful, decode the JSON
            if ($response->successful()) {
                $data = $response->json();

                // Check if price is available in the response
                if (isset($data['body'][0]['regularMarketPrice'])) {
                    return $data['body'][0]['regularMarketPrice'];
                } else {
                    Log::error("No price data found for ticker {$ticker}");
                    return null;
                }
            } else {
                // Log any error response from the API
                Log::error("Error fetching data from Yahoo Finance API: " . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Exception occurred while fetching price for ticker {$ticker}: " . $e->getMessage());
            return null;
        }
    }
}
