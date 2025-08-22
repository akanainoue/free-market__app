<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Like;

class LikeToggleTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_user_can_like_a_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['likes_count' => 0]);

        $this->actingAs($user)
            ->post("/item/{$product->id}/like");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertEquals(1, $product->fresh()->likes_count);
    }

    public function test_user_can_unlike_a_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['likes_count' => 1]);

        // 事前にいいねしておく
        Like::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->post("/item/{$product->id}/like");

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertEquals(0, $product->fresh()->likes_count);
    }

    public function test_guest_cannot_like_product()
    {
        $product = Product::factory()->create();

        $response = $this->post("/item/{$product->id}/like");

        $response->assertRedirect('/login'); // 認証ミドルウェアがある前提
        $this->assertEquals(0, $product->fresh()->likes_count);
    }
}
