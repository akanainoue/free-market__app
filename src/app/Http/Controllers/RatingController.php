<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class RatingController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $raterId = Auth::id();

        $rateeId = ($raterId === $transaction->buyer_id)
            ? $transaction->product->user_id
            : $transaction->buyer_id;

        Rating::create([
            'transaction_id' => $transaction->id,
            'rater_id' => $raterId,
            'ratee_id' => $rateeId,
            'score' => $request->score,
            'comment' => $request->comment,
        ]);

        // ステータス変更
        $transaction->update(['status' => 'completed', 'completed_at' => now()]);

        // 通知メール送信
        Mail::to($transaction->product->user->email)->send(new TransactionCompletedMail($transaction));

        return redirect('/')->with('status', '取引を完了しました');
    }
}
