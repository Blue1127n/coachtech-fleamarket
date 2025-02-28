<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PaymentController;
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

Route::middleware(['guest'])->group(function () {
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/email/verify', function () {
    \Log::info('Email verification check', ['user' => Auth::user()]);
    if (Auth::user()->hasVerifiedEmail()) {
        return redirect()->route('mypage.profile');
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::get('/', [ItemController::class, 'index'])->name('products.index');
Route::get('/mylist', [ItemController::class, 'mylist'])->name('products.mylist');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::post('/item/{item_id}/like', [ItemController::class, 'like'])->middleware('auth')->name('item.like');
Route::post('/item/{item_id}/comment', [ItemController::class, 'comment'])->middleware('auth')->name('item.comment');
Route::get('/purchase/{item_id}', [ItemController::class, 'purchase'])->name('item.purchase');
Route::post('/purchase/{item_id}', [ItemController::class, 'processPurchase'])->middleware('auth')->name('item.processPurchase');
Route::get('/purchase/address/{item_id}', [ItemController::class, 'changeAddress'])->name('item.changeAddress');
Route::post('/purchase/address/{item_id}', [ItemController::class, 'updateAddress'])->name('item.updateAddress');
Route::get('/profile/purchases', [UserProfileController::class, 'purchasedItems'])->name('profile.purchases');
Route::get('/sell', [ItemController::class, 'create'])->middleware('auth')->name('item.create');
Route::post('/sell', [ItemController::class, 'store'])->middleware('auth')->name('item.store');

Route::middleware(['auth'])->group(function () {
Route::get('/payment/{item_id}', [PaymentController::class, 'showPaymentPage'])->name('payment.page');
Route::post('/payment/checkout/{item_id}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::get('/payment/success/{transaction_id}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel/{transaction_id}', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/mypage', [UserProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [UserProfileController::class, 'edit'])->name('mypage.profile');
    Route::put('/mypage/profile', [UserProfileController::class, 'update'])->name('mypage.profile.update');
    Route::get('/mypage/items', [UserProfileController::class, 'listItems'])->name('mypage.items');
});

