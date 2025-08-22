<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'comment' => $this->faker->sentence,
        ];
    }
}
