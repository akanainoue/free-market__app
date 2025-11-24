@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    {{-- サイドバー --}}
    <aside class="sidebar">
        <h2>その他の取引</h2>
        @foreach($transactions as $t)
            <a href="{{ route('transaction.chat', $t->id) }}" class="sidebar-item">
                {{ $t->product->name }}
            </a>
        @endforeach
    </aside>

    {{-- メインチャット画面 --}}
    <main class="chat-main">
        {{-- ヘッダー --}}
        <div class="chat-header">
            <h2>「{{ $partner->name }}」さんとの取引画面</h2>
            @if(Auth::id() === $transaction->buyer_id)
                <button onclick="openModal()" class="rate-button">取引を完了する</button>
            @endif
        </div>

        {{-- 商品情報 --}}
        <div class="product-info">
            <div class="product-image">
                <img src="{{ asset('storage/items/' . $product->image) }}" alt="商品画像" class="product-img">
            </div>
            <div class="product-details">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price">¥{{ number_format($product->price) }}</div>
            </div>
        </div>

        {{-- メッセージ一覧 --}}
        <div class="message-list message-container">
            @foreach($messages as $message)
                <div class="message-wrapper {{ $message->sender_id === auth()->id() ? 'self' : 'other' }}">
                    <div class="message-header">
                        @if ($message->sender_id === auth()->id())
                            <div class="message-user-info right">
                                <span class="username">{{ $message->sender->name }}</span>
                                <img class="avatar" src="{{ asset('storage/profile_image/' . $message->sender->profile_image) }}" alt="プロフィール画像">
                            </div>
                        @else
                            <div class="message-user-info left">
                                <img class="avatar" src="{{ asset('storage/profile_image/' . $message->sender->profile_image) }}" alt="プロフィール画像">
                                <span class="username">{{ $message->sender->name }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="message-box">
                        @if ($message->image_path)
                            <img src="{{ asset('storage/chat_images/' . $message->image_path) }}" class="chat-image">
                        @endif
                        <p>{{ $message->message }}</p>
                        
                    </div>
                    <div class="meta">
                        <span class="time">{{ $message->created_at->format('H:i') }}</span>
                        @if($message->sender_id === auth()->id())
                            <!-- <a href="#">編集</a> -->
                            <form action="{{ route('transaction.message.edit', $message->id) }}" method="GET" class="inline">
                                <button type="submit">編集</button>
                            </form>
                            <form action="{{ route('transaction.message.delete', $message->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit">削除</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- メッセージ送信フォーム --}}
        
        <form id="chat-form" method="POST" action="{{ route('transaction.message.store', $transaction->id) }}" class="chat-form" enctype="multipart/form-data">
            @csrf

            @if ($errors->has('message'))
                <div class="error-message">{{ $errors->first('message') }}</div>
            @endif
            @if ($errors->has('image'))
                <div class="error-message">{{ $errors->first('image') }}</div>
            @endif
            
            <input type="text" name="message" id="chat-input" placeholder="取引メッセージを記入してください">
            <label for="imageUpload" class="image-label">画像を追加</label>
            <input type="file" name="image" id="imageUpload" style="display: none;">
            <button type="submit" class="send-button">
                <img src="{{ asset('images/send-4008.svg') }}" alt="送信">
            </button>
        </form>
        
    </main>
</div>

{{-- ⭐ 評価モーダル --}}
<dialog id="rateModal" class="rate-modal">
    <form method="POST" action="{{ route('transaction.rate', $transaction->id) }}">
        @csrf
        <div class="modal-header">
            <h2>取引が完了しました。</h2>
        </div>
        <hr class="divider">

        <p class="rating-label">今回の取引相手はどうでしたか？</p>

        <div class="star-rating">
            @for ($i = 1; $i <= 5; $i++)
                <input type="radio" name="score" id="star{{ $i }}" value="{{ $i }}" {{ $i === 3 ? 'checked' : '' }}>
                <label for="star{{ $i }}">★</label>
            @endfor
        </div>
        <hr class="divider">

        <div class="modal-actions">
            <button type="submit" class="submit-btn">送信する</button>
        </div>
    </form>
</dialog>
@endsection

@section('scripts')
<script>
    function openModal() {
        document.getElementById('rateModal').showModal();
    }
    function closeModal() {
        document.getElementById('rateModal').close();
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('chat-input');
    const form = document.getElementById('chat-form');

    const transactionId = "{{ $transaction->id }}";
    const draftKey = `chat_draft_${transactionId}`;

    // 入力を復元
    const saved = localStorage.getItem(draftKey);
    if (saved) {
        input.value = saved;
    }

    // 入力内容を保存
    input.addEventListener('input', () => {
        localStorage.setItem(draftKey, input.value);
    });

    // 送信時に削除
    form.addEventListener('submit', () => {
        localStorage.removeItem(draftKey);
    });
});
</script>

<script>
    // const transactionId = "{{ $transaction->id }}";

    // window.Echo.private(`chat.${transactionId}`)
    //     .listen('NewChatMessage', (e) => {
    //         const container = document.querySelector('.message-container');
    //         const div = document.createElement('div');
    //         div.className = 'p-2 border rounded bg-gray-100';
            
    //         //XSS対策
    //         const name = document.createElement('p');
    //         name.textContent = `${e.sender_name}: ${e.message}`;
    //         const time = document.createElement('span');
    //         time.textContent = e.created_at;
    //         time.className = 'text-xs text-gray-500';

    //         div.appendChild(name);

    //         // 👇 ここが画像表示処理の追加部分
    //         if (e.image_path) {
    //             const img = document.createElement('img');
    //             img.src = `/storage/chat_images/${e.image_path}`;
    //             img.className = 'chat-image';
    //             div.appendChild(img);
    //         }

    //         div.appendChild(time);
    //         container.appendChild(div);
    //     });
</script>
@endsection



