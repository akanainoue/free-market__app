@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
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
<div class="tab-menu">
    <a href="/" class="{{ !$isMylist ? 'active' : '' }}">おすすめ</a>
    <a href="/?page=mylist" class="{{ $isMylist ? 'active' : '' }}">マイリスト</a>
</div>
<div class="item-list">
    @foreach($items as $item)
    <div class="item">
    <a class="item-link" href="/item/{{$item->id}}" class="item-card-link">
        <div class="item-card">
            <img class="item-image" src="{{ asset('storage/items/' . $item->image) }}" alt="{{ $item->name }}">
            <div class="description">
                <span>{{ $item->name }}</span>
            @if ($item->purchase)
                <span class="sold-label">Sold</span>
            @endif
            </div>
        </div>
    </a>
    </div>
    @endforeach
</div>
<div class="pagination">
{{ $items->appends(request()->query())->links()}}
</div>
@endsection