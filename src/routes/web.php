<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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

// ログイン/会員登録
Route::middleware(['guest'])->group(function () {
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
});

// ログアウト（認証済みのユーザー専用）
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// メール認証関連
Route::get('/email/verify', function () {
    \Log::info('Email verification check', ['user' => Auth::user()]);
    if (Auth::user()->hasVerifiedEmail()) {
        return redirect()->route('mypage.profile'); // すでに認証済みの場合、プロフィール設定画面にリダイレクト
    }
    return view('auth.verify-email'); // 認証待ちの場合に認証画面を表示
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// トップページ & マイリスト
Route::get('/', [ItemController::class, 'index'])->name('products.index'); // 商品一覧
Route::get('/mylist', [ItemController::class, 'mylist'])->name('products.mylist'); // マイリスト

// 商品関連
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show'); // 商品詳細
Route::post('/item/{item_id}/like', [ItemController::class, 'like'])->middleware('auth')->name('item.like');// いいねのルート
Route::post('/item/{item_id}/comment', [ItemController::class, 'comment'])->middleware('auth')->name('item.comment');// コメントのルート
Route::get('/purchase/{item_id}', [ItemController::class, 'purchase'])->name('item.purchase'); // 商品購入
Route::get('/purchase/address/{item_id}', [ItemController::class, 'changeAddress'])->name('item.changeAddress'); // 住所変更
Route::get('/sell', [ItemController::class, 'create'])->name('item.create'); // 商品出品

// プロフィール関連
Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/mypage', [UserProfileController::class, 'show'])->name('mypage'); // プロフィール確認
    Route::get('/mypage/profile', [UserProfileController::class, 'edit'])->name('mypage.profile'); // プロフィール編集
    Route::put('/mypage/profile', [UserProfileController::class, 'update'])->name('mypage.profile.update'); // プロフィール更新
    Route::get('/mypage/items', [UserProfileController::class, 'listItems'])->name('mypage.items'); // 購入・出品商品一覧（パラメータで切り替え）
});

