<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function testCreateShouldStoreNewSymbol()
    {
        $symbol = $this->faker->lexify("????");
        $data = [
            'symbol' => $symbol,
        ];
        $response = $this->post(route('stocks.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stocks', $data);
    }
}
