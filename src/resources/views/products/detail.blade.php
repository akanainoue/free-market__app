@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}" />
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
<div class="product-detail-container">
    <div class="product-image">
        <img src="{{ asset('storage/items/' . $product->image) }}" alt="商品画像">
    </div>

    <div class="product-info">
        <h2 class="product-title">{{ $product->name }}</h2>
        <p class="brand-name">{{ $product->brand_name }}</p>
        <p class="price">¥{{ number_format($product->price) }} <span>（税込）</span></p>

        <div class="icon-row">
            <div class="icon-block">
                <form class="icon" action="/item/{{ $product->id }}/like" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="like-button {{ $product->likes->contains('user_id', auth()->id()) ? 'liked' : '' }}">
                        ★
                    </button>
                </form>
                <span class="count">{{ $product->likes_count }}</span>
            </div>
            <div class="icon-block">
                <span class="icon comment">💬</span> 
                <span class="count comment">{{ $product->reviews_count }}</span>
            </div>
        </div>

        @if ($product->transaction && $product->transaction->status === 'completed')
            <a href="{{ route('purchase.form', ['item_id' => $product->id]) }}" class="purchase-button">購入手続きへ</a>
        @else
            <a href="{{ route('transaction.enter', ['product' => $product->id]) }}" class="purchase-button">購入手続きへ</a>
        @endif
        
        <h3 class="section-title">商品説明</h3>
        <p>{!! nl2br(e($product->description)) !!}</p>

        <h3 class="section-title">商品の情報</h3>
        <p><strong>カテゴリー</strong>&nbsp&nbsp&nbsp<span class="category-name">
        @foreach ($product->categories as $category)
            {{ $category->name }}</span></p>
        @endforeach
        <p><strong>商品の状態</strong>&nbsp&nbsp&nbsp {{ $product->condition->name }}</p>

        <h3 class="section-title">コメント({{ $product->reviews->count() }})</h3>
        @foreach($product->reviews as $review)
            <div class="comment">
                <div class="user-info">
                    <img class="profile-img" src="{{ asset('storage/' . $review->user->profile_image) }}">
                    <strong>{{ $review->user->name }}</strong>
                </div>
                <p class="comment-body">{{ $review->comment }}</p>
            </div>
        @endforeach

        @auth
        <form action="/item/{{ $product->id }}/review" method="POST">
            @csrf
            <label>商品へのコメント</label>
            <textarea name="comment" rows="4" class="form-textarea" required></textarea>
            <button type="submit" class="comment-submit">コメントを送信する</button>
        </form>
        @endauth
    </div>
</div>
@endsection


