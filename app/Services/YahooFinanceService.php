<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class YahooFinanceService
{
    protected $retryDelay = 1; // Start with a 1-second delay for retries
    protected $maxRetries = 5; // Maximum number of retry attempts

    /**
     * Fetch the actual price for a ticker symbol.
     *
     * @param string $ticker
     * @return float|null
     */
    public function getPrice(string $ticker): ?float
    {
        // Try to fetch from cache first, with a cache lifetime of 5 minutes
        $cacheKey = 'ticker_price_' . $ticker;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Fetch from Yahoo Finance with retries and exponential backoff
        $price = $this->fetchPriceWithRetry($ticker);

        if (!is_null($price)) {
            // Store the price in cache for 5 minutes to reduce API load
            Cache::put($cacheKey, $price, now()->addMinutes(5));
        }

        return $price;
    }

    /**
     * Fetch price from Yahoo Finance with retry and exponential backoff.
     *
     * @param string $ticker
     * @return float|null
     */
    protected function fetchPriceWithRetry(string $ticker): ?float
    {
        $attempts = 0;
        $maxRetries = 3;
        $retryDelay = 3;  // Start with a 1-second delay for retries

        while ($attempts < $maxRetries) {
            try {
                $attempts++;
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}";
                $response = file_get_contents($url);

                if ($response === false) {
                    throw new \Exception("Failed to fetch data");
                }

                $data = json_decode($response, true);

                // Extract price from response data
                if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                    return $data['chart']['result'][0]['meta']['regularMarketPrice'];
                } else {
                    Log::error("Price data not found for ticker {$ticker}");
                    return null;
                }

            } catch (\Exception $e) {
                Log::error("Error fetching actual price for ticker {$ticker}: " . $e->getMessage());

                // Implement exponential backoff for retries
                $backoffTime = $retryDelay * pow(2, $attempts);
                sleep($backoffTime);  // Wait before retrying
            }
        }

        // Log failure after max attempts
        Log::error("Max retries reached. Failed to fetch price for ticker {$ticker}");
        return null;
    }
}
