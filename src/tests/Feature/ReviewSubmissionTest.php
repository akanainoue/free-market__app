<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ReviewSubmissionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_authenticated_user_can_submit_review()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$product->id}/review", [
            'comment' => 'とても素晴らしい商品です。',
        ]);

        $response->assertRedirect(); // back() なのでリダイレクトを確認
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'comment' => 'とても素晴らしい商品です。',
        ]);
    }

    public function test_guest_cannot_submit_review()
    {
        $product = Product::factory()->create();

        $response = $this->post("/item/{$product->id}/review", [
            'comment' => 'コメント投稿テスト',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('reviews', [
            'comment' => 'コメント投稿テスト',
        ]);
    }

    public function test_validation_error_when_comment_is_empty()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$product->id}/review", [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment']);
    }

    public function test_validation_error_when_comment_exceeds_255_characters()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->post("/item/{$product->id}/review", [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors(['comment']);
    }
}
