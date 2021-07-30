<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Stock;
use App\Models\Quote;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateShouldStoreNewSymbol()
    {
        $symbol = 'TBNB';
        $data = [
            'symbol' => $symbol,
        ];
        $response = $this->postJson(route('stocks.store'), $data);

        $response->assertStatus(201)
                 ->assertExactJson($data);
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

    public function testCreateShouldFailWithSpacesInSymbol()
    {
        $symbol = "TB NB";
        $data = [
            'symbol' => $symbol,
        ];
        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('stocks', $data);
    }

    public function testCreateShouldFailWithTooLongSymbol()
    {
        $symbol = "AVERYVERYLONGSYMBOLNAMETHATHASMORETHAN50CHARACTERSS";
        $data = [
            'symbol' => $symbol,
        ];
        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('stocks', $data);
    }

    public function testCreateShouldFailWithDuplicatedSymbol()
    {
        //create a stock with same symbol to force the error on next request
        $symbol = Stock::factory()->create()->symbol;
        $data = [
            'symbol' => $symbol,
        ];

        $response = $this->postJson(route('stocks.store'), $data);
        $response->assertStatus(422);
    }

    public function testDestroyShouldDeleteSymbol()
    {
        $symbol = Stock::factory()->create()->symbol;

        $response = $this->deleteJson(route('stocks.destroy',$symbol));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('stocks', ['symbol' => $symbol]);
    }
    
    public function testDestroyShouldDeleteSymbolWithQuotes()
    {
        $stock = Stock::factory()->create();
        Quote::factory(1)->create(['stock_id'=>$stock->id]);

        $response = $this->deleteJson(route('stocks.destroy',$stock->symbol));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('stocks', ['symbol' => $stock->symbol]);
    }

    public function testDestroyShouldFailWithNonExistingSymbol()
    {
        $symbol = 'MYNEWSYMBOL';

        $response = $this->deleteJson(route('stocks.destroy',$symbol));
        $response->assertStatus(404);
    }
}
