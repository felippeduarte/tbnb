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
            'stocks' => [
                [
                    'quote' => '10.59',
                    'symbol' => $symbol,
                ],
            ],
        ];

        $response = $this->postJson(route('quotes.store'), $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'date' => $data['date'],
        ]);
    }

    public function testStoreExistentQuoteShouldWork()
    {
        $symbol = "SYMBOL";
        Stock::factory()->create(['symbol' => $symbol]);

        $data = [
            'date' => '2021-07-28',
            'stocks' => [
                [
                    'quote' => '10.59',
                    'symbol' => $symbol,
                ],
            ],
        ];

        $this->postJson(route('quotes.store'), $data);
        $response = $this->postJson(route('quotes.store'), $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'date' => $data['date'],
        ]);
    }

    public function testStoreBulkQuotesShouldWork()
    {
        $symbol1 = "SYMBOL1";
        Stock::factory()->create(['symbol' => $symbol1]);
        $symbol2 = "SYMBOL2";
        Stock::factory()->create(['symbol' => $symbol2]);

        $data = [
            'date' => '2021-07-28',
            'stocks' => [
                [
                    'quote' => '10.59',
                    'symbol' => $symbol1,
                ],
                [
                    'quote' => '0.66',
                    'symbol' => $symbol2,
                ],
            ],
        ];

        $this->postJson(route('quotes.store'), $data);
        $response = $this->postJson(route('quotes.store'), $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'date' => $data['date'],
        ]);
        $this->assertDatabaseHas('quotes', [
            'quote' => $data['stocks'][1]['quote'],
            'date' => $data['date'],
        ]);
    }

    public function testStoreIncorrectDateShouldFail()
    {
        $symbol = "SYMBOL";
        $stock = Stock::factory()->create(['symbol' => $symbol]);

        $data = [
            'date' => '2021-06-32',
            'stocks' => [
                [
                    'quote' => '10.59',
                    'symbol' => $symbol,
                ],
            ],
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'stock_id' => $stock->id,
        ]);

        $data['date'] = '';
        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'stock_id' => $stock->id,
        ]);

        $data['date'] = \Carbon\Carbon::now()->addDays(1);
        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'stock_id' => $stock->id,
        ]);
    }

    public function testStoreIncorrectQuoteShouldFail()
    {
        $symbol = "SYMBOL";
        $stock = Stock::factory()->create(['symbol' => $symbol]);

        $data = [
            'date' => '2021-06-28',
            'stocks' => [
                [
                    'quote' => '10.59a',
                    'symbol' => $symbol,
                ],
            ],
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $data['quote'] = '';
        $response = $this->postJson(route('quotes.store'), $data);
        $response->assertStatus(422);

        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'stock_id' => $stock->id,
        ]);
    }

    public function testStoreNonExistentSymbolShouldFail()
    {
        $symbol = "SYMBOL";

        $data = [
            'date' => '2021-07-28',
            'stocks' => [
                [
                    'quote' => '10.59',
                    'symbol' => $symbol,
                ],
            ],
        ];

        $response = $this->postJson(route('quotes.store'), $data);
        
        $response->assertStatus(422);

        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'date' => $data['date'],
        ]);
    }

    public function testStoreBulkNonExistentSymbolShouldFail()
    {
        $symbol1 = "SYMBOL1";
        $stock = Stock::factory()->create(['symbol' => $symbol1]);
        $symbol2 = "SYMBOL2";

        $data = [
            'date' => '2021-07-28',
            'stocks' => [
                [
                    'quote' => '10.59',
                    'symbol' => $symbol1,
                ],
                [
                    'quote' => '0.66',
                    'symbol' => $symbol2,
                ],
            ],
        ];

        $response = $this->postJson(route('quotes.store'), $data);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][0]['quote'],
            'stock_id' => $stock->id,
            'date' => $data['date'],
        ]);
        $this->assertDatabaseMissing('quotes', [
            'quote' => $data['stocks'][1]['quote'],
            'date' => $data['date'],
        ]);
    }
}