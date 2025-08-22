<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function show()
    {
        return view('auth.verify');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill(); // メール認証完了
        return redirect('/mypage/profile');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/mypage/profile');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '確認メールを再送信しました。');
    }
}
