<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;
use App\Events\UnreadCountUpdated;
use App\Http\Requests\StoreChatMessageRequest;

class TransactionMessageController extends Controller
{
    // 送信
    public function store(StoreChatMessageRequest $request, Transaction $transaction)
    {
        $validated = $request->validated();

        // 認可確認
        if (!in_array(Auth::id(), [$transaction->buyer_id, $transaction->product->user_id])) {
            abort(403);
        }



        // 画像の保存処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
            $imagePath = basename($imagePath);
             
            // $imagePath = basename($request->file('image')->store('chat_images', 'public'));
        }

        // メッセージの保存
        $message = TransactionMessage::create([
            'transaction_id' => $transaction->id,
            'sender_id' => Auth::id(),
            'message' =>$validated['message'] ?? null, 
            'image_path' => $imagePath,
        ]);


        broadcast(new NewChatMessage($message))->toOthers(); // ←これがリアルタイム通信

        // 受信者のIDを判定
        $receiverId = Auth::id() === $transaction->buyer_id
            ? $transaction->product->user_id
            : $transaction->buyer_id;
        
        // 未読メッセージ数のカウント
        $newUnreadCount = $transaction->messages()
            ->where('sender_id', '!=', $receiverId)
            ->where('is_read', false)
            ->count();
        
        // 未読数更新イベントを発火（リアルタイム通知）
        event(new UnreadCountUpdated($receiverId, $newUnreadCount));

        return redirect()->route('transaction.chat', $transaction);
    }

    // 削除
    public function destroy(TransactionMessage $message)
    {
        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return back()->with('status', 'メッセージを削除しました');
    }
}
