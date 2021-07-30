<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Stock;

class SimulatedPriceService
{
    const SMA_FAST_PERIOD = 8;
    const SMA_SLOW_PERIOD = 20;
    
    /**
     * Simulate value based on Simple Moving Average
     * If FAST >= SLOW then add random value from 0 to FAST - SLOW
     * If FAST <  SLOW then sub random value from 0 to SLOW - FAST
     */
    public function getSimulatedPrice()
    {
        //manual query fetch top N lastest quotes for each stock
        //and retrieve the SLOW and FAST average
        $sql = "WITH latestQuotes AS (
            SELECT s.symbol, q.quote, ROW_NUMBER() 
            OVER (
                PARTITION BY stock_id 
                ORDER BY date DESC
            ) AS rowNumber 
            FROM quotes q
            INNER JOIN stocks s ON s.id = q.stock_id
        )
        SELECT symbol, MAX(price) AS price, SUM(sma_slow) AS sma_slow, SUM(sma_fast) AS sma_fast
        FROM
        (
            SELECT symbol, 0 AS price, AVG(quote) AS sma_slow, 0 AS sma_fast
                FROM latestQuotes WHERE rowNumber <= ?
                GROUP BY symbol
            UNION ALL
            SELECT symbol, 0, 0, AVG(quote)
                FROM latestQuotes WHERE rowNumber <= ?
                GROUP BY symbol
            UNION ALL 
                SELECT symbol, quote, 0, 0
                FROM latestQuotes WHERE rowNumber = 1
        ) x
        GROUP BY symbol ORDER BY symbol";
        $lastQuotesByStocks = \DB::select($sql, [self::SMA_SLOW_PERIOD, self::SMA_FAST_PERIOD]);
        $lastQuotes = [];

        //foreach quote, apply simulated price based on SMAs difference
        foreach($lastQuotesByStocks as $q) {
            $smaDifference = $q->sma_fast - $q->sma_slow;
            $randomSimulatedValue = rand(0, (int)100*($smaDifference))/100;

            $lastQuotes[] = [
                'symbol' => $q->symbol,
                'simulatedPrice' => round($q->price + $randomSimulatedValue,2),
            ];
        }

        return $lastQuotes;
    }
}