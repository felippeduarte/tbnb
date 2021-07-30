<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Stock;
use App\Models\Quote;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexWithoutSymbolShouldReturn404()
    {
        $response = $this->getJson(route('quotes.index'));
        $response->assertStatus(404);
    }

    public function testIndexWithLimitShouldGetLimitSymbolQuotes()
    {
        $symbol = "SYMBOL";
        $limit = 2;
        $stock = Stock::factory()->create(['symbol' => $symbol]);
        $quotes = Quote::factory(2*$limit)->create(['stock_id' => $stock->id]);
        
        $response = $this->getJson(route('quotes.index', ['symbol' => $symbol, 'limit'=> $limit]));
        
        $response->assertStatus(200);
        $response->assertJsonCount($limit);
    }

    public function testIndexWithSymbolShouldGetSymbolQuotes()
    {
        $symbol = "SYMBOL";
        $stock = Stock::factory()->create(['symbol' => $symbol]);
        $quotes = Quote::factory(10)->create(['stock_id' => $stock->id]);
        $response = $this->getJson(route('quotes.index', ['symbol' => $symbol]));
        
        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'quote' => $quotes[0]->quote,
                'date' => $quotes[0]->date,
            ]
        );
        $response->assertJsonFragment(
            [
                'quote' => $quotes[9]->quote,
                'date' => $quotes[9]->date,
            ]
        );
    }

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