@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>コンビニ支払い情報</h2>

        @if ($konbini)
            <p><strong>支払い番号:</strong> {{ $konbini->payment_code }}</p>
            <p><strong>コンビニ名:</strong> {{ $konbini->store }}</p>
            <p><strong>支払期限:</strong> {{ \Carbon\Carbon::parse($konbini->expires_at)->toDateTimeString() }}</p>
            <p>上記情報をもとに、選択したコンビニでお支払いください。</p>
        @else
            <p>支払い情報が見つかりませんでした。</p>
        @endif

        <a href="/mypage" class="btn btn-primary">マイページへ</a>
    </div>
@endsection

