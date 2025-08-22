<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Like;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_products_are_retrieved_except_my_own()
    {
        $user = User::factory()->create(); // ログインユーザー
        $otherUser = User::factory()->create();

        // 他人が出品した商品
        $product1 = Product::factory()->create(['user_id' => $otherUser->id, 'name' => 'Product A']);
        $product2 = Product::factory()->create(['user_id' => $otherUser->id, 'name' => 'Product B']);

        // 自分が出品した商品
        $myProduct = Product::factory()->create(['user_id' => $user->id, 'name' => 'My Product']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Product A');
        $response->assertSee('Product B');
        $response->assertDontSee('My Product'); // 自分の商品は表示されない
    }

    public function test_sold_products_show_sold_label()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $soldProduct = Product::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Sold Item',
        ]);

        // 購入済み（purchase レコードがある）
        Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $soldProduct->id,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSee('Sold Item');
    }

    public function test_unsold_products_do_not_show_sold_label()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Available Product',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Available Product');
        $response->assertDontSee('Sold'); // Sold 表示されてはいけない
    }

    public function test_only_liked_products_are_shown_in_mylist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $notLiked = Product::factory()->create();

        // いいね登録
        // $user->likes()->create(['product_id' => $product->id]);
        $product->likes()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertSee($product->name);
        $response->assertDontSee($notLiked->name);
    }


    public function test_mylist_shows_sold_label_if_product_was_purchased_by_someone_or_user()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $otherBuyer = User::factory()->create();

        // 商品A: 他人が購入
        $productA = Product::factory()->create(['user_id' => $seller->id]);
        Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productA->id,
        ]);
        Purchase::factory()->create([
            'user_id' => $otherBuyer->id,
            'product_id' => $productA->id,
        ]);

        // 商品B: 自分が購入
        $productB = Product::factory()->create(['user_id' => $seller->id]);
        Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productB->id,
        ]);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productB->id,
        ]);

        // 商品C: 未購入
        $productC = Product::factory()->create(['user_id' => $seller->id]);
        Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productC->id,
        ]);

        $response = $this->actingAs($user)->get('/?page=mylist');
        $response->assertStatus(200);

        // 3商品とも名前が表示されている
        $response->assertSee($productA->name);
        $response->assertSee($productB->name);
        $response->assertSee($productC->name);

        // Soldラベルは2つの商品に表示（productA, productB）
        $response->assertSeeText('Sold');
    }

    public function test_mylist_shows_nothing_for_guests()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        // 本来ならログインユーザーがこの商品にいいねしている前提
        Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        $response = $this->get('/?page=mylist');
        $response->assertDontSee($product->name);
        $response->assertStatus(200);
    }

    public function test_guest_can_search_by_partial_product_name()
    {
        $matched = Product::factory()->create(['name' => 'Super Camera']);
        $unmatched = Product::factory()->create(['name' => 'Laptop']);
    
        $response = $this->get('/?keyword=Camera');
    
        $response->assertStatus(200);
        $response->assertSee('Super Camera');
        $response->assertDontSee('Laptop');
    }


}
