<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserProfileController extends Controller
{
    // プロフィール画面を表示
    public function show(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 'sell'); // デフォルトは出品した商品
        $items = $page === 'sell' ? $user->items : $user->purchasedItems;

        return view('profile.index', compact('user', 'items', 'page'));
    }

    // プロフィール編集画面（初回ログイン時含む）を表示
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // プロフィールを更新
    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        \Log::info('Update method started', ['request' => $addressRequest->all()]);

        dd('フォームリクエストが実行されています');

        // バリデーション失敗時のエラーを確認
    if (session()->has('errors')) {
        \Log::info('Session has errors', ['errors' => session()->get('errors')->all()]); // エラー内容を記録
        dd(session()->get('errors')->all());
    }

    \Log::info('Validation passed, processing update'); // バリデーションが成功したことを記録

        // バリデーション済みデータを取得
        $validatedAddress = $addressRequest->validated();
        $validatedProfile = $profileRequest->validated();

        \Log::info('Validated data', [
            'address' => $validatedAddress,
            'profile' => $validatedProfile,
        ]);

        // ユーザー情報を更新
        $user = auth()->user();
        $user->update([
            'name' => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address' => $validatedAddress['address'],
            'building' => $validatedAddress['building'] ?? null,
        ]);

        \Log::info('User profile updated', ['user' => $user->toArray()]);

        // プロフィール画像がアップロードされた場合
        if ($profileRequest->hasFile('profile_image')) {
            $path = $profileRequest->file('profile_image')->store('public/profile_images');
            $user->profile_image = str_replace('public/', '', $path);
            $user->save();

            \Log::info('Profile image updated', ['path' => $path]);
        }

        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました');
    }

    // 購入した商品一覧
    public function purchasedItems()
    {
        $items = Auth::user()->transactions()->with('item')->get(); // ユーザーの購入した商品を取得
        return view('profile.purchased', compact('items')); // 購入商品一覧ビュー
    }

    // 出品した商品一覧
    public function soldItems()
    {
        $items = Auth::user()->items()->where('status_id', Status::SOLD)->get();
        return view('profile.sold', compact('items')); // 出品商品一覧ビュー
    }
}
