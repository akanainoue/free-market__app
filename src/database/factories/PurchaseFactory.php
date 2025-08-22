<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;


class PurchaseFactory extends Factory
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
            'payment_method' => '1',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都渋谷区',
            'delivery_building_name' => 'ビル101',
        ];
    }
}
