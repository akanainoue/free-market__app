@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}" />
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
<div class="mypage-container">
    <div class="profile-section">
        <div class="profile-image-wrapper">
            <img src="{{ asset('storage/profiles/' . $user->profile_image) }}" alt="" class="profile-img">
            <p class="profile-alt-text">プロフィール画像</p>
        </div>
        <h2 class="user-name">{{ $user->name }}</h2>
        <a href="/mypage/profile/edit" class="edit-profile-button">プロフィールを編集</a>
    </div>

    <div class="tab-menu">
        <a href="/mypage?page=sell" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="/mypage?page=buy" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="product-list">
        @if($page === 'sell')
            @foreach($products as $product)
                <div class="product-card">
                    <img src="{{ asset('storage/items/' . $product->image) }}" alt="商品画像">
                    <p>{{ $product->name }}</p>
                </div>
            @endforeach
        @elseif($page === 'buy')
            @foreach($purchases as $purchase)
                <div class="product-card">
                    <img src="{{ asset('storage/items/' . $purchase->product->image) }}" alt="商品画像">
                    <p>{{ $purchase->product->name }}</p>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection