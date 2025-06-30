@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
<link rel="stylesheet" href="{{ asset('css/edit.css') }}" />
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
<div class="profile-form-container">
    <h2 class="title">プロフィール設定</h2>

    <form action="/mypage/profile/edit" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="image-upload-section">
            <img class="profile-img" src="{{ asset('storage/' . $user->profile_image) }}">
            <label class="image-select-btn">
                画像を選択する
                <input type="file" name="profile_image" hidden>
            </label>
            <p class="form__error-message">
            @error('profile_image')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
            <p class="form__error-message">
            @error('name')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
            <p class="form__error-message">
            @error('postal_code')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
            <p class="form__error-message">
            @error('address')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name', $user->building_name) }}">
        </div>

        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>
@endsection