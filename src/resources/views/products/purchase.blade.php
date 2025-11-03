@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
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
<div class="purchase-container">
    <div class="left">
        <img src="{{ asset('storage/items/' . $product->image) }}" alt="商品画像" class="product-image">
        <div class="img-info">
            <h2>{{ $product->name }}</h2>
            <p class="price">¥{{ number_format($product->price) }}</p>
        </div>

        <div class="payment-section">
            <form method="GET" action="/purchase/{{ $product->id }}">
                <h3>支払い方法</h3>
                <select name="payment_method" onchange="this.form.submit()" required>
                    <option value="">選択してください</option>
                    <option value="1" {{ request('payment_method') == '1' ? 'selected' : '' }}>カード支払い</option>
                    <option value="2" {{ request('payment_method') == '2' ? 'selected' : '' }}>コンビニ支払い</option>
                </select>
            </form>
        </div>
        <p class="form__error-message">
            @error('payment_method')
            {{ $message }}
            @enderror
        </p>

        <div class="address-section">
            <div class="section-header">
                <h3>配送先</h3>
                <a href="/purchase/address/{{$product->id}}">変更する</a>
            </div>
            <div class="address">
                <p>〒 {{ $user->postal_code }}</p>
                <p>{{ $user->address }} {{ $user->building_name }}</p>
            </div>
        </div>
        <p class="form__error-message">
            @error('delivery_address')
            {{ $message }}
            @enderror
        </p>
    </div>

    <div class="right-summary">
        <table class="summary-table">
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($product->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td>
                    @if(request('payment_method') === '1')
                        カード支払い
                    @elseif(request('payment_method') === '2')
                        コンビニ支払い
                    @else
                        未選択
                    @endif
                </td>
            </tr>
        </table>

        <form method="POST" action="/purchase/{{ $product->id }}">
            @csrf
            <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
            <input type="hidden" name="delivery_postal_code" value="{{ $user->postal_code }}">
            <input type="hidden" name="delivery_address" value="{{ $user->address }}">
            <input type="hidden" name="building_name" value="{{ $user->building_name }}">

            <button type="submit" class="buy-button">購入する</button>
        </form>
    </div>
</div>
@endsection

