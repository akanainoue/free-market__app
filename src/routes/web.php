<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// --- 公開ルート（未ログインでもOK） --- //

// トップ（全商品一覧）＋マイリスト切替（クエリ判定）
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// 会員登録 / ログイン（Fortifyでカスタマイズ可）
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// --- 認証が必要なルート --- //
Route::middleware('auth')->group(function () {

    // 商品詳細
    Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('products.show');

    // 商品購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.show');

    // 住所変更（購入中）
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');

    // 商品出品
    Route::get('/sell', [ProductController::class, 'create'])->name('products.create');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');

    // プロフィール（マイページ）
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});

