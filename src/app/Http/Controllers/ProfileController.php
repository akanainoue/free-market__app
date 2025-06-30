<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //ここから下追記
use App\Http\Requests\ProfileRequest;
use App\Models\User;



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
            $path = $request->file('profile_image')->store('public/profile_image');
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

        if ($page === 'buy') {
            $purchases = $user->purchases()->with('product')->latest('created_at')->get();
            $products = collect(); // 空コレクションで安全に
        } else {
            $products = $user->products()->latest('created_at')->get();
            $purchases = collect();
        }

        return view('profile.show', compact('user', 'products', 'purchases', 'page'));
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
            $path = $request->file('profile_image')->store('public/profile_image');
            $validated['profile_image'] = basename($path);
        }

        $user->update($validated);

        return redirect('/mypage');
    }
}
