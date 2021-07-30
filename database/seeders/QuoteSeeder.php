<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Quote;
use Carbon\Carbon;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $stock_id = Stock::factory()->create()->id;
        $date = Carbon::today();
        $quote = $faker->numberBetween(1,200);
        for($i = 100; $i > 0; $i--) {
            
            Quote::factory()->create([
                'stock_id' => $stock_id,
                'date' => $date,
                'quote' => $quote,
            ]);
            $date->subDays(1);
            $priceVariation = ($quote*0.05)*100;
            $quote = $quote + ($faker->numberBetween($quote-$priceVariation, $quote+$priceVariation)/100);
        }
        
    }
}
