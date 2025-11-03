<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // 商品詳細 → 取引がなければ作成
    public function enter(Product $product)
    {
        $buyerId = Auth::id();

        $transaction = Transaction::firstOrCreate([
            'buyer_id' => $buyerId,
            'product_id' => $product->id,
        ]);

        return redirect()->route('transaction.chat', $transaction);
    }

    // チャット画面表示
    public function chat(Transaction $transaction)
    {
        // 認可チェック（出品者 or 購入者のみ）
        abort_unless(
            Auth::id() === $transaction->buyer_id || Auth::id() === $transaction->product->user_id,
            403
        );

        $messages = $transaction->messages()->with('sender')->orderBy('created_at')->get();

        // 相手ユーザー
        $partner = (Auth::id() === $transaction->buyer_id)
            ? $transaction->product->user
            : $transaction->buyer;

        // ✅ ← ここで product を定義！
        $product = $transaction->product;

        // サイドバー用：そのユーザーの全取引
        $transactions = Transaction::with('product')
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('is_read', false)->where('sender_id', '!=', Auth::id());
            }])
            ->where(function ($q) {
                $q->where('buyer_id', Auth::id())
                  ->orWhereHas('product', function ($q) {
                      $q->where('user_id', Auth::id());
                  });
            })->where('status', 'ongoing')->get();

        return view('transactions.chat', compact('transaction', 'product', 'messages', 'partner', 'transactions'));
    }

    public function complete(Transaction $transaction)
    {
        $transaction->update(['status' => 'completed', 'completed_at' => now()]);
    
        // 通知メール
        Mail::to($transaction->product->user->email)->send(
            new TransactionCompletedMail($transaction)
        );
    
        return redirect('/')->with('status', '取引が完了しました');
    }
}
