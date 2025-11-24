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
        // 先に作られたユーザーを取得
        $userA = User::where('email', 'usera@example.com')->first();
        $userB = User::where('email', 'userb@example.com')->first();

        // 念のため null チェック（ログ出しなど）
        // if (!$userA || !$userB) {
        //     dump("ユーザーA または B が存在しません");
        //     return;
        // }

        // 商品データ
        $products = [
            // 商品1〜5 → userA
            [
                'name' => '腕時計', 
                'price' => 15000, 
                'image' => 'Armani+Mens+Clock.jpg', 
                'condition_id' => 1, 
                'description' => 'スタイリッシュなデザインのメンズ腕時計', 
                'user_id' => $userA->id
            ],
            [
                'name' => 'HDD', 
                'price' => 5000, 
                'image' => 'HDD+Hard+Disk.jpg', 
                'condition_id' => 2, 
                'description' => '高速で信頼性の高いハードディスク', 
                'user_id' => $userA->id
            ],
            [
                'name' => '玉ねぎ3束', 
                'price' => 300, 
                'image' => 'Food+onion.jpg', 
                'condition_id' => 3, 
                'description' => '新鮮な玉ねぎ3束のセット', 
                'user_id' => $userA->id
            ],
            [
                'name' => '革靴', 
                'price' => 4000, 
                'image' => 'Leather+Shoes.jpg', 
                'condition_id' => 4, 
                'description' => 'クラシックなデザインの革靴', 
                'user_id' => $userA->id
            ],
            [
                'name' => 'ノートPC', 
                'price' => 45000, 
                'image' => 'Living+Room+Laptop.jpg', 
                'condition_id' => 1, 
                'description' => '高性能なノートパソコン', 
                'user_id' => $userA->id
            ],

            // 商品6〜10 → userB
            [
                'name' => 'マイク', 
                'price' => 8000, 
                'image' => 'Music+Mic.jpg', 
                'condition_id' => 2, 
                'description' => '高音質のレコーディング用マイク', 
                'user_id' => $userB->id
            ],
            [
                'name' => 'ショルダーバッグ', 
                'price' => 3500, 
                'image' => 'Purse+fashion+pocket.jpg', 
                'condition_id' => 3, 
                'description' => 'おしゃれなショルダーバッグ', 
                'user_id' => $userB->id
            ],
            [
                'name' => 'タンブラー', 
                'price' => 500, 
                'image' => 'Tumbler+souvenir.jpg', 
                'condition_id' => 4, 
                'description' => '使いやすいタンブラー', 
                'user_id' => $userB->id
            ],
            [
                'name' => 'コーヒーミル', 
                'price' => 4000, 
                'image' => 'Waitress+with+Coffee+Grinder.jpg', 
                'condition_id' => 1, 
                'description' => '手動のコーヒーミル', 
                'user_id' => $userB->id
            ],
            [
                'name' => 'メイクセット', 
                'price' => 2500,  
                'image' => 'makeup.jpg', 
                'condition_id' => 2, 
                'description' => '便利なメイクアップセット',  
                'user_id' => $userB->id
            ],
        ];

        DB::table('products')->insert($products);
    }
}

