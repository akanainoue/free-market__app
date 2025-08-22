<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Like;
use App\Models\Review;

class ProductDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_product_detail_displays_all_necessary_information()
    {
        $user = User::factory()->create();
        $reviewer1 = User::factory()->create();
        $reviewer2 = User::factory()->create();
        $condition = Condition::factory()->create(['name' => '良好']);
        $category1 = Category::factory()->create(['name' => '家電']);
        $category2 = Category::factory()->create(['name' => 'カメラ']);

        // 商品作成
        $item = Product::factory()->create([
            'user_id' => $user->id,
            'name' => '高性能カメラ',
            'brand_name' => 'CANON',
            'image' => 'sample.jpg',
            'price' => 50000,
            'description' => 'とても良いカメラです。',
            'condition_id' => $condition->id,
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);

        // いいねとコメント作成
        Like::factory()->count(3)->create([
            'product_id' => $item->id,
        ]);
        Review::factory()->create([
            'product_id' => $item->id,
            'user_id' => $reviewer1->id,
            'comment' => '素晴らしい商品でした！',
        ]);
        Review::factory()->create([
            'product_id' => $item->id,
            'user_id' => $reviewer2->id,
            'comment' => 'まあまあです。',
        ]);
        $response = $this->get("/item/{$item->id}");

        // ステータス確認
        $response->assertStatus(200);

        // 商品情報の確認
        $response->assertSee('高性能カメラ');
        $response->assertSee('CANON');
        $response->assertSee('¥50,000'); // フォーマットに応じて調整
        $response->assertSee('とても良いカメラです。');
        $response->assertSee('sample.jpg');
        $response->assertSee('良好');

        // カテゴリ表示
        $response->assertSee('家電');
        $response->assertSee('カメラ');

        // いいね数・コメント数（withCountされている）
        $response->assertSee((string) $item->likes()->count());
        $response->assertSee((string) $item->reviews()->count());

        $response->assertSee('3'); // いいね数
        $response->assertSee('2'); // レビュー数

        // コメント内容とユーザー名
        $response->assertSee($reviewer1->name);
        $response->assertSee('素晴らしい商品でした！');

        $response->assertSee($reviewer2->name);
        $response->assertSee('まあまあです。');
    }
}
