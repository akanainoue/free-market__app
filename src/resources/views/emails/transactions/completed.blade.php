@component('mail::message')
# 取引完了のお知らせ

こんにちは、{{ $transaction->product->user->name }} さん。

あなたが出品された商品「**{{ $transaction->product->name }}**」が取引完了となりました。

---

- 購入者：{{ $transaction->buyer->name }}
- 取引完了日時：{{ optional($transaction->completed_at)->format('Y年m月d日 H:i') }}
- 商品価格：¥{{ number_format($transaction->product->price) }}

---

今後とも COACHTECHフリマ をご利用いただけますと幸いです。

@component('mail::button', ['url' => url('/')])
商品一覧を見る
@endcomponent

ありがとうございます！  
COACHTECHフリマ運営チーム
@endcomponent

