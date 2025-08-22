<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Like;
use App\Models\User;
use App\Models\Product;

class LikeFactory extends Factory
{
    protected $model = Like::class;
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
        ];
    }
}
