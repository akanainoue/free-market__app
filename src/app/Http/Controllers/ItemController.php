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



class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isMylist = $request->query('page') === 'mylist';
        // クエリパラメータが ?page=mylist のときに $isMylist を true にします
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
            $likedIds = $user->likes()->pluck('product_id');
            $query->whereIn('id', $likedIds);
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

    public function search(Request $request)
    {
        $query = Product::with('categories');

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $items = $query->paginate(6);
        $categories = Category::all();

        return view('products.index', compact('items', 'categories'));
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
            $path = $request->file('image')->store('public/items');
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
        $product = Product::with(['categories', 'condition', 'reviews', 'likes'])->withCount(['likes', 'reviews'])->findOrFail($item_id);
        return view('products.detail', compact('product'));
    }

    public function toggle($item_id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($item_id);

        if ($product->likes()->where('user_id', $user->id)->exists()) {
            $product->likes()->where('user_id', $user->id)->delete();
        } else {
            $product->likes()->create(['user_id' => $user->id]);
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

        // Purchase テーブルへ保存処理
        $product->purchase()->create([
            'user_id' => $user->id,
            'payment_method' => $validated['payment_method'],
            'delivery_postal_code'=> $user->postal_code,
            'delivery_address'=> $user->address,
        ]);


        return redirect('/mypage')->with('success', '購入が完了しました。');
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
