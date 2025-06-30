@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}" />
@endsection

@section('nav')
<div class="header-input">
    <form class="search-form" action="/" method="GET" class="search-form">
        <input class="keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}" >
        <!-- ボタンは不要。Enterで送信される -->
    </form>
</div>
@if (Auth::check())
<div class="nav-buttons">
    <form class="logout__btn" action="/logout" method="post">
        @csrf
        <button>ログアウト</button>
    </form>
    <form action="/mypage" class="mypage-link" method="get">
        <button>マイページ</button>
    </form>
    <form action="/sell" class="sell-link" method="get">
        <button>出品</button>
    </form>
</div>
@endif
@endsection

@section('content')
<h2 class="content-title">住所の変更</h2>
<div class="address-edit__form">
    <form action="/purchase/address/{{ $item_id }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="delivery_postal_code" value="{{ old('delivery_postal_code', $user->postal_code) }}">
            <p class="form__error-message">
            @error('delivery_postal_code')
            {{ $message }}
            @enderror
        </p>
        </div>
        <div class="form-group">
            <label>住所</label>
            <input type="text" name="delivery_address" value="{{ old('delivery_address', $user->address) }}">
            <p class="form__error-message">
            @error('delivery_address')
            {{ $message }}
            @enderror
        </p>
        </div>
        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="delivery_building_name" value="{{ old('delivery_building_name', $user->building_name) }}">
            <p class="form__error-message">
            @error('delivery_building_name')
            {{ $message }}
            @enderror
        </p>
        </div>
        <button type="submit">更新する</button>
    </form>
</div>
@endsection