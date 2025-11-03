<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //ここから下追記
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Condition;
use App\Models\Review;
use App\Models\Like;
use App\Models\Purchase;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
// stripe
use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\PaymentIntent;



class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isMylist = $request->query('tab') === 'mylist';
        // クエリパラメータが ?tab=mylist のときに $isMylist を true にします
        $keyword = $request->input('keyword');

        $query = Product::query()
            ->with(['categories', 'condition', 'purchase']);

        // ログインユーザーがいる場合のみ「自分の出品商品を除外」
        if ($user) {
            $query->where('user_id', '!=', $user->id);
        }

        // リレーションも含めたキーワード検索
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                    ->orWhere('brand_name', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%")
                    ->orWhereHas('categories', function ($subQ) use ($keyword) {
                        $subQ->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('condition', function ($subQ) use ($keyword) {
                        $subQ->where('name', 'like', "%$keyword%");
                    });
            });
        }

        if ($isMylist) {
            if (!$user) {
                // 非ログインなら結果0件にする
                $query->whereRaw('0 = 1');
            } else {
                $likedIds = $user->likes()->pluck('product_id');
                $query->whereIn('id', $likedIds);
            }
        }

        $items = $query->latest()->paginate(6);
        $categories = \App\Models\Category::all();
        $conditions = \App\Models\Condition::all();

        return view('products.index', compact(
            'items',
            'keyword',
            'categories',
            'conditions',
            'isMylist'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('products.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated(); // バリデーション済のデータ取得

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image'] = basename($path);
        }

        $validated['user_id'] = auth()->id();
        $product = Product::create($validated);

        if ($request->filled('category_id')) {
        $product->categories()->sync($request->input('category_id'));
        }

        return redirect('/mypage');
    }

    public function detail($item_id)
    {
        $product = Product::with(['categories', 'condition', 'reviews', 'likes', 'transaction'])->withCount(['likes', 'reviews'])->findOrFail($item_id);
        return view('products.detail', compact('product'));
    }

    public function toggle($item_id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($item_id);

        if ($product->likes()->where('user_id', $user->id)->exists()) {
            $product->likes()->where('user_id', $user->id)->delete();
            $product->decrement('likes_count');
        } else {
            $product->likes()->create(['user_id' => $user->id]);
            $product->increment('likes_count');
        }

        return back();
    }

    public function review(CommentRequest $request,$item_id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($item_id);
        $validated = $request->validated();
        $product->reviews()->create([
            'user_id' => $user->id,
            'comment' => $validated['comment']
        ]);

        return back();
    }

    public function purchaseForm($item_id)
    {
        $product = Product::findOrFail($item_id);
        $user = Auth::user();

        return view('products.purchase', compact('product', 'user'));
    }

    public function buy(PurchaseRequest $request, $item_id)
    {
        $product = Product::findOrFail($item_id);
        $user = Auth::user();

        $validated = $request->validated();

        Stripe::setApiKey(config('services.stripe.secret'));

        // ✅ 注: カード/コンビニの選択は Stripe の画面で行う
        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $product->name],
                    'unit_amount' => $product->price,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $user->email,
            // 成功時: session_id を渡して success ハンドラへ
            'success_url' => route('purchase.success', [
                'item_id' => $product->id,
            ]) . '?session_id={CHECKOUT_SESSION_ID}',
            // キャンセル時
            'cancel_url' => route('purchase.cancel', ['item_id' => $product->id]),
        ]);

        return redirect()->away($session->url);
    }


    public function checkoutSuccess(Request $request, $item_id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($item_id);

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect('/mypage')->with('error', '決済情報が見つかりません。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Checkout Session を取得
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        // PaymentIntent を取得し、支払い完了を検証
        $paymentIntentId = $session->payment_intent;
        if (!$paymentIntentId) {
            return redirect('/mypage')->with('error', '決済が完了していません。');
        }

        $pi = \Stripe\PaymentIntent::retrieve($paymentIntentId);

        // 安全側で支払い完了状態のみ通す
        // paid or succeeded 判定（どちらでも良いが両方見る）
        $isPaid = ($session->payment_status === 'paid') || ($pi->status === 'succeeded');
        if (!$isPaid) {
            return redirect('/mypage')->with('error', '決済が未完了です。');
        }

        // 支払い手段の種別を 1=カード / 2=コンビニ にマッピング
        // 1) チャージ明細から確定的に取る（あれば）
        $mapped = 1; // デフォルト=カード
        try {
            $charge = $pi->charges->data[0] ?? null;
            $detailType = $charge->payment_method_details->type ?? null; // 'card' or 'konbini'
            if ($detailType === 'konbini') {
                $mapped = 2;
            }
        } catch (\Throwable $e) {
            // フォールバック: PaymentIntentに宣言されたtypeから推測
            $firstType = $pi->payment_method_types[0] ?? 'card';
            if ($firstType === 'konbini') {
                $mapped = 2;
            }
        }

        // 二重作成防止（hasOne想定なら exists チェック）
        if (!$product->purchase()->where('user_id', $user->id)->exists()) {
            $product->purchase()->create([
                'user_id' => $user->id,
                'payment_method' => $mapped, // 1=カード, 2=コンビニ
                'delivery_postal_code'   => $user->postal_code,
                'delivery_address'       => $user->address,
                'delivery_building_name' => $user->building_name,
            ]);
        }

        return redirect('/mypage')->with('success', '決済が完了しました。');
    }

    public function checkoutCancel($item_id)
    {
        return redirect('/mypage')->with('error', '決済がキャンセルされました。');
    }

    public function editAddress ($item_id)
    {
        $user = Auth::user();
        return view('products.address', compact('user', 'item_id'));
    }

    // 更新処理
    public function updateAddress (AddressRequest $request, $item_id)
    {
        $user = Auth::user();
        $user->update([
            'postal_code' => $request->delivery_postal_code,
            'address' => $request->delivery_address,
            'building_name' => $request->delivery_building_name,
        ]);

        return redirect('/purchase/' . $item_id)->with('message', '住所を更新しました');
    }
}


