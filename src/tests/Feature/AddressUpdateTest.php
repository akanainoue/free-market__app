<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Condition;

class AddressUpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_user_can_update_address_and_see_it_on_purchase_page()
    {
        $user = User::factory()->create([
            'postal_code' => '111-1111',
            'address' => '旧住所',
            'building_name' => '旧ビル',
        ]);

        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $this->actingAs($user)->put("/purchase/address/{$product->id}", [
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '新しい住所',
            'delivery_building_name' => '新しいビル',
        ]);

        // DBが更新されていることを確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '新しい住所',
            'building_name' => '新しいビル',
        ]);

        // 購入ページに新しい住所が表示されることを確認
        $response = $this->actingAs($user)->get("/purchase/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee('123-4567');
        $response->assertSee('新しい住所');
        $response->assertSee('新しいビル');
    }

    public function test_updated_address_is_saved_with_purchase()
    {
        $user = User::factory()->create([
            'postal_code' => '888-8888',
            'address' => '購入時住所',
            'building_name' => '購入時ビル',
        ]);

        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $this->actingAs($user)->post("/purchase/{$product->id}", [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 1,
            'delivery_postal_code' => $user->postal_code,
            'delivery_address' => $user->address,
            'delivery_building_name' => $user->building_name,
        ]);

        // 購入時の配送先が購入テーブルに記録されていること
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 1,
            'delivery_postal_code' => '888-8888',
            'delivery_address' => '購入時住所',
            'delivery_building_name' => '購入時ビル',
        ]);
    }
}
