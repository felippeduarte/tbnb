<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\SimulatedPriceService;
use App\Models\Stock;
use App\Models\Quote;
use Carbon\Carbon;

class SimulatedPriceTest extends TestCase
{
    use RefreshDatabase;

    public function testSimulatedPriceShouldMustVaryBetweenSmasValues()
    {
        $fastPeriod = SimulatedPriceService::SMA_FAST_PERIOD;
        $slowPeriod = SimulatedPriceService::SMA_SLOW_PERIOD;

        $stock1 = Stock::factory()->create();
        $stock2 = Stock::factory()->create();
        $date = Carbon::now()->subDays($slowPeriod);

        //insert some data
        for($i = 1; $i <= $slowPeriod; $i++) {
            $price = 20;
            $quote1 = [
                'date' => $date,
                'quote' => $price+($i/10),
            ];
            $quote2 = [
                'date' => $date,
                'quote' => $price-($i/10),
            ];
            $prices[] = $quote1['quote'];
            Quote::factory()->create($quote1 + ['stock_id' => $stock1->id]);
            Quote::factory()->create($quote2 + ['stock_id' => $stock2->id]);

            $date->addDay();
        }
        
        $lastPrice1 = $quote1['quote'];
        $lastPrice2 = $quote2['quote'];

        //calculate SMA difference between fast and slow.
        //the new value difference should not be greater/less than this value
        $smaFast = array_sum(array_slice($prices, -$fastPeriod))/$fastPeriod;
        $smaSlow = array_sum(array_slice($prices, -$slowPeriod))/$slowPeriod;
        $smaDifference = $smaFast - $smaSlow;

        $response = $this->getJson(route('simulatedPrice.index'));

        //first stock should increase it's value
        $this->assertGreaterThanOrEqual($lastPrice1, $response[0]['simulatedPrice']);
        $this->assertLessThanOrEqual($lastPrice1 + $smaDifference, $response[0]['simulatedPrice']);

        //second stock should decrease it's value
        $this->assertLessThan($lastPrice2, $response[1]['simulatedPrice']);
        $this->assertGreaterThan($lastPrice2 - $smaDifference, $response[1]['simulatedPrice']);
    }
}
