<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //ここから下追記
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Transaction;



class ProfileController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        return view('profile.profile', compact('user'));
    }

    public function store(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->only(['name', 'postal_code', 'address', 'building_name']);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_image', 'public');
            $data['profile_image'] = basename($path);
        }

        // 初回プロフィール登録
        $user->update($data);

        return redirect('/');
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell'); // デフォルトは出品した商品

        $products = collect();
        $purchases = collect();
        $transactions = collect();

        if ($page === 'buy') {
            $purchases = $user->purchases()->with('product')->latest('created_at')->get();
            $products = collect(); // 空コレクションで安全に
        } elseif ($page === 'transaction') {
            $transactions = Transaction::where('status', 'ongoing')
                ->where(function ($query) use ($user) {
                    $query->where('buyer_id', $user->id)
                          ->orWhereHas('product', function ($q) use ($user) {
                              $q->where('user_id', $user->id);
                          });
                })
                ->with('product')
                ->withCount(['messages as unread_count' => function ($q) use ($user) {
                    $q->where('is_read', false)
                      ->where('sender_id', '!=', $user->id);
                }])
                ->get();
            // $transactions = $user->transactions()
            //     ->where('status', 'ongoing')
            //     ->with('product')
            //     ->withCount(['messages as unread_count' => function ($q) use ($user) {
            //         $q->where('is_read', false)->where('sender_id', '!=', $user->id);
            //     }])
            //     ->get();    
        } else {
            $products = $user->products()->latest('created_at')->get();
        }

        // 取引中の商品
        // $transactions = auth()->user()->transactions()
        //     ->where('status', 'ongoing')
        //     ->with('product')
        //     ->withCount(['messages as unread_count' => function ($q) {
        //         $q->where('is_read', false)->where('sender_id', '!=', auth()->id());
        //     }])
        //     ->get();

        return view('profile.show', compact('user', 'products', 'purchases', 'page', 'transactions'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->only(['name', 'postal_code', 'address', 'building_name','profile_image']);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_image', 'public');
            $validated['profile_image'] = basename($path);
        }

        $user->update($validated);

        return redirect('/mypage');
    }
}

