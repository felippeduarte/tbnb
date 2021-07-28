<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Stock;

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
        $response = $this->postJson(route('stocks.store'), $data);

        $response->assertStatus(201)
                 ->assertJson($data);
        $this->assertDatabaseHas('stocks', $data);
    }

    public function testCreateShouldFailWithNoInput()
    {
        $data = [];
        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
    }

    public function testCreateShouldFailWithEmptySymbol()
    {
        $symbol = "";
        $data = [
            'symbol' => $symbol,
        ];
        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('stocks', $data);
    }

    public function testCreateShouldFailWithTooLongSymbol()
    {
        $symbol = $this->faker->regexify('[A-Z]{51}');
        $data = [
            'symbol' => $symbol,
        ];
        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('stocks', $data);
    }

    public function testCreateShouldFailWithDuplicatedSymbol()
    {
        $symbol = $this->faker->lexify("????");
        $data = [
            'symbol' => $symbol,
        ];

        //create a stock with same symbol to force the error on next request
        Stock::factory()->create($data);

        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
    }

    public function testDestroyShouldDeleteSymbol()
    {
        $id = Stock::factory()->create()->id;

        $response = $this->deleteJson(route('stocks.destroy',$id));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('stocks', ['id' => $id]);
    }

    public function testDestroyShouldWorkEvenWithNonExistingSymbol()
    {
        $id = Stock::max('id') + 1;

        $response = $this->deleteJson(route('stocks.destroy',$id));
        $response->assertStatus(200);
    }
}
