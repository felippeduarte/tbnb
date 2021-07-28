<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Stock;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreNonExistentQuoteShouldWork()
    {
        $symbol = "SYMBOL";
        Stock::factory()->create(['symbol' => $symbol]);

        $data = [
            'date' => '2021-07-28',
            'quote' => '10.59',
            'symbol' => $symbol,
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        
        $response->assertStatus(200)
                 ->assertJson($data);

        //remove symbol for database assert
        unset($data['symbol']);

        $this->assertDatabaseHas('quotes', $data);
    }

    public function testStoreExistentQuoteShouldWork()
    {
        $symbol = "SYMBOL";
        Stock::factory()->create(['symbol' => $symbol]);

        $data = [
            'date' => '2021-07-28',
            'quote' => '10.59',
            'symbol' => $symbol,
        ];

        $this->postJson(route('quotes.store'), $data);
        $response = $this->postJson(route('quotes.store'), $data);
        
        $response->assertStatus(200)
                 ->assertJson($data);

        //remove symbol for database assert
        unset($data['symbol']);

        $this->assertDatabaseHas('quotes', $data);
    }

    public function testStoreIncorrectDateShouldFail()
    {
        $symbol = "SYMBOL";

        $data = [
            'date' => '2021-06-31',
            'quote' => '10.59',
            'symbol' => $symbol,
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $data['date'] = '';
        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        //remove symbol for database assert
        unset($data['symbol']);
        $this->assertDatabaseMissing('quotes', $data);
    }

    public function testStoreIncorrectQuoteShouldFail()
    {
        $symbol = "SYMBOL";

        $data = [
            'date' => '2021-06-31',
            'quote' => '10.59a',
            'symbol' => $symbol,
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $data['quote'] = '';
        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        //remove symbol for database assert
        unset($data['symbol']);
        $this->assertDatabaseMissing('quotes', $data);
    }

    public function testStoreNonExistentSymbolShouldFail()
    {
        $symbol = "SYMBOL";

        $data = [
            'date' => '2021-07-28',
            'quote' => '10.59',
            'symbol' => $symbol,
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        
        $response->assertStatus(422);

        //remove symbol for database assert
        unset($data['symbol']);
        $this->assertDatabaseMissing('quotes', $data);
    }
}