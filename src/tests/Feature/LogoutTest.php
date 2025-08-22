<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_logout()
    {
        // 事前にログイン状態を作る
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/'); // ログアウト後のリダイレクト先
        $this->assertGuest(); // セッションが破棄されていること（ログアウト状態）を確認
    }
}
