<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'stock_id' => Stock::factory(),
            'date' => $this->faker->date(),
            'quote' => $this->faker->randomFloat(1, 1, 1000),
        ];
    }
}
