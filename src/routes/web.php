<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MailSendController;

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionMessageController;
use App\Http\Controllers\RatingController;

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

Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/email/verify', [VerificationController::class, 'show']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'detail']);



// --- 認証が必要なルート --- //
Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'create'])
        ->middleware('verified');

    Route::put('/mypage/profile', [ProfileController::class, 'store']);

    Route::post('/item/{item_id}/like', [ItemController::class, 'toggle']);

    Route::post('/item/{item_id}/review', [ItemController::class, 'review']);

    // 商品購入
    Route::get('/purchase/{item_id}', [ItemController::class, 'purchaseForm'])->name('purchase.form');
    Route::post('/purchase/{item_id}', [ItemController::class, 'buy']);
    // Checkout成功/キャンセル（ビューは作らず /mypage に戻す）
    Route::get('/purchase/success/{item_id}', [ItemController::class, 'checkoutSuccess'])
    ->name('purchase.success');
    Route::get('/purchase/cancel/{item_id}', [ItemController::class, 'checkoutCancel'])
    ->name('purchase.cancel');

    // 商品詳細 → チャット画面に遷移（取引作成 or 既存取得）
    Route::get('/transaction/{product}', [TransactionController::class, 'enter'])->name('transaction.enter');
    // チャット画面表示
    Route::get('/transaction/chat/{transaction}', [TransactionController::class, 'chat'])->name('transaction.chat');
    // メッセージ送信
    Route::post('/transaction/chat/{transaction}/message', [TransactionMessageController::class, 'store'])->name('transaction.message.store');
    // メッセージ削除
    Route::delete('/transaction/chat/message/{message}', [TransactionMessageController::class, 'destroy'])->name('transaction.message.delete');
    // 取引完了（評価入力）
    Route::post('/transaction/{transaction}/rate', [RatingController::class, 'store'])->name('transaction.rate');

    // 住所変更（購入中）
    Route::get('/purchase/address/{item_id}', [ItemController::class, 'editAddress']);
    Route::put('/purchase/address/{item_id}', [ItemController::class, 'updateAddress']);

    // 商品出品
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell', [ItemController::class, 'store']);

    // プロフィール（マイページ）
    Route::get('/mypage', [ProfileController::class, 'show']);
    Route::get('/mypage/profile/edit', [ProfileController::class, 'edit']);
    Route::put('/mypage/profile/edit', [ProfileController::class, 'update']);

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    });
});


