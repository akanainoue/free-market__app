<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // ユーザーと紐づけ
            'condition_id' => Condition::factory(), // コンディションと紐づけ
            'name' => $this->faker->word(),
            'brand_name' => $this->faker->optional()->company(),
            'image' => 'default.jpg', // 画像アップロードは手動処理のため仮のファイル名でOK
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(100, 10000),
            'likes_count' => 0,
            'reviews_count' => 0,
        ];
    }

}
