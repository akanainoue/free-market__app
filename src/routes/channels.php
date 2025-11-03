<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{transactionId}', function ($user, $transactionId) {
    // return true; // 認可制御したければここを修正これは全ユーザーが参加可能という状態
    return $transaction &&
           ($user->id === $transaction->buyer_id || $user->id === $transaction->product->user_id);
    //これにより「購入者」か「出品者」しかそのチャットチャンネルに入れなくなります
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});