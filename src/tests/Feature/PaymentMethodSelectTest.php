<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Condition;

class PaymentMethodSelectTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function makeUserAndProduct(): array
    {
        $user = User::factory()->create([
            'postal_code'   => '100-0001',
            'address'       => '東京都千代田区1-1-1',
            'building_name' => 'テストビル101',
            'email'         => 'u@example.com',
        ]);

        $condition = Condition::factory()->create();
        $product = Product::factory()->create([
            'condition_id' => $condition->id,
            'price'        => 12345,
            'name'         => 'テスト商品',
            'image'        => 'dummy.jpg',
        ]);

        return [$user, $product];
    }

    public function test_no_param_shows_unselected()
    {
        [$user, $product] = $this->makeUserAndProduct();

        $response = $this->actingAs($user)->get("/purchase/{$product->id}");

        $response->assertStatus(200);
        // 要約欄に「未選択」
        $response->assertSee('未選択');

        // hidden の payment_method が空になっている
        $response->assertSee('name="payment_method" value=""', false);

        // どちらの option にも selected が付いていない
        $response->assertDontSee('option value="1" selected', false);
        $response->assertDontSee('option value="2" selected', false);
    }

    public function test_selecting_card_reflects_on_summary()
    {
        [$user, $product] = $this->makeUserAndProduct();

        $response = $this->actingAs($user)->get("/purchase/{$product->id}?payment_method=1");

        $response->assertStatus(200);
        // 要約欄がカード支払い
        $response->assertSee('カード支払い');
        $response->assertDontSee('未選択');

        // select の方でカードが選択状態
        $response->assertSee('option value="1" selected', false);
        $response->assertDontSee('option value="2" selected', false);

        // hidden の値も 1
        $response->assertSee('name="payment_method" value="1"', false);
    }

    public function test_selecting_konbini_reflects_on_summary()
    {
        [$user, $product] = $this->makeUserAndProduct();

        $response = $this->actingAs($user)->get("/purchase/{$product->id}?payment_method=2");

        $response->assertStatus(200);
        // 要約欄がコンビニ支払い
        $response->assertSee('コンビニ支払い');
        $response->assertDontSee('未選択');

        // select の方でコンビニが選択状態
        $response->assertSee('option value="2" selected', false);
        $response->assertDontSee('option value="1" selected', false);

        // hidden の値も 2
        $response->assertSee('name="payment_method" value="2"', false);
    }
}
