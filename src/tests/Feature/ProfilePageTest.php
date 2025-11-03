<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Condition;
use App\Models\Purchase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_user_profile_page_displays_correct_information()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'profile_image' => 'test-image.jpg',
        ]);

        // 商品を出品（productsテーブル）
        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => '出品商品1',
            'image' => 'item1.jpg',
        ]);

        // 商品を購入（purchasesテーブル）
        $purchasedProduct = Product::factory()->create([
            'condition_id' => $condition->id,
            'name' => '購入商品1',
            'image' => 'item2.jpg',
        ]);
        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $purchasedProduct->id,
            'payment_method' => 1,
            'delivery_postal_code' => '111-1111',
            'delivery_address' => '東京都テスト町',
            'delivery_building_name' => 'テストビル101',
        ]);

        // 出品一覧ページを確認
        $this->actingAs($user)
            ->get('/mypage?page=sell')
            ->assertStatus(200)
            ->assertSee('テスト太郎')
            ->assertSee('出品商品1')
            ->assertSee("storage/profile_image/{$user->profile_image}")
            ->assertSee("storage/items/{$product->image}");

        // 購入一覧ページを確認
        $this->actingAs($user)
            ->get('/mypage?page=buy')
            ->assertStatus(200)
            ->assertSee('購入商品1')
            ->assertSee("storage/items/{$purchasedProduct->image}");
    }
}
