<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Condition;
use App\Models\Purchase;


class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_authenticated_user_can_purchase_product()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building_name' => 'テストビル101'
        ]);

        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)->post("/purchase/{$product->id}", [
            'payment_method' => '1',
        ]);

        $response->assertRedirect('/mypage');
        $response->assertSessionHas('success', '購入が完了しました。');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => '1',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都新宿区',
            'delivery_building_name' => 'テストビル101',
        ]);
    }
}
