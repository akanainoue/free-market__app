@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
<div class="verify-container">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <a class="verify-button" href="http://localhost:8025" target="_blank">認証はこちらから</a>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button class="resend-link" type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection

