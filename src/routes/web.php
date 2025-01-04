<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserProfileController;

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
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// トップページ & マイリスト
Route::get('/', [ItemController::class, 'index'])->name('products.index'); // 商品一覧
Route::get('/mylist', [ItemController::class, 'mylist'])->name('products.mylist'); // マイリスト

// 商品関連
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show'); // 商品詳細
Route::get('/purchase/{item_id}', [ItemController::class, 'purchase'])->name('item.purchase'); // 商品購入
Route::get('/purchase/address/{item_id}', [ItemController::class, 'changeAddress'])->name('item.changeAddress'); // 住所変更
Route::get('/sell', [ItemController::class, 'create'])->name('item.create'); // 商品出品

// プロフィール関連
Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/mypage', [UserProfileController::class, 'show'])->name('profile.mypage');// プロフィール確認
    Route::get('/mypage/profile', [UserProfileController::class, 'edit'])->name('profile.edit');// プロフィール編集
    Route::post('/mypage/profile', [UserProfileController::class, 'update'])->name('profile.update');// プロフィール更新
    Route::get('/mypage/buy', [UserProfileController::class, 'purchasedItems'])->name('profile.purchased'); // 購入商品一覧
    Route::get('/mypage/sell', [UserProfileController::class, 'soldItems'])->name('profile.sold'); // 出品商品一覧
});