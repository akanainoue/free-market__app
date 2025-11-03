@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="profile-form-container">
    <h2 class="title">プロフィール設定</h2>

    <form action="/mypage/profile" method="POST" enctype="multipart/form-data">
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
            <input type="text" name="name" value="{{ old('name') }}" required>
            <p class="form__error-message">
            @error('name')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code') }}" required>
            <p class="form__error-message">
            @error('postal_code')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address') }}" required>
            <p class="form__error-message">
            @error('address')
            {{ $message }}
            @enderror
            </p>
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name') }}">
        </div>

        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>
@endsection

