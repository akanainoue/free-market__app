<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        // 登録されているユーザーIDの配列を取得
        $products = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'Armani+Mens+Clock.jpg',
                'condition_id' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'HDD+Hard+Disk.jpg',
                'condition_id' => 2,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'Food+onion.jpg',
                'condition_id' => 3,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'Leather+Shoes.jpg',
                'condition_id' => 4,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'Living+Room+Laptop.jpg',
                'condition_id' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'Music+Mic.jpg',
                'condition_id' => 2,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'Purse+fashion+pocket.jpg',
                'condition_id' => 3,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'Tumbler+souvenir.jpg',
                'condition_id' => 4,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'Waitress+with+Coffee+Grinder.jpg',
                'condition_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => '外出メイクアップセット.jpg',
                'condition_id' => 2,
            ],
        ];

        // 各商品に user_id をランダム割り当て
        foreach ($products as &$product) {
            $product['user_id'] = collect($userIds)->random();
        }

        DB::table('products')->insert($products);
    
    }
}
