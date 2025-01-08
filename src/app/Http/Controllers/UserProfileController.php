<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    public function update(ProfileRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // ユーザー情報を更新
        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('public/profile_images');
            $user->profile_image = str_replace('public/', '', $path);
            $user->save();
        }

        return redirect()->route('profile.mypage')->with('success', 'プロフィールが更新されました');
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
