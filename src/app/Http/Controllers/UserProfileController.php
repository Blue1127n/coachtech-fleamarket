<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserProfileController extends Controller
{
    // プロフィール画面を表示
    public function show(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 'sell'); // デフォルトは出品した商品
        $items = $page === 'sell' ? $user->items : $user->purchasedItems;

        // items が null の場合は空のコレクションを返す
        $items = $items ?? collect();

        \Log::info('Profile items data', [
            'user' => $user->id,
            'page' => $page,
            'items_count' => $items->count(),
        ]);


        return view('profile.show', compact('user', 'items', 'page'));
    }

    // プロフィール編集画面（初回ログイン時含む）を表示
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // プロフィールを更新
    public function update(Request $request, AddressRequest $addressRequest, ProfileRequest $profileRequest)
{
    \Log::info('プロフィール更新開始', ['user_id' => Auth::id()]);

    try {
        // バリデーションの開始ログ
        $validatedAddress = $addressRequest->validated();
        $validatedProfile = $profileRequest->validated();
        \Log::info('バリデーション成功', ['user_id' => Auth::id()]);

        // ユーザー情報更新
        $user = auth()->user();
        $user->update([
            'name' => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address' => $validatedAddress['address'],
            'building' => $validatedAddress['building'] ?? null,
        ]);

        // 画像アップロード
        if ($profileRequest->hasFile('profile_image')) {
            $file = $profileRequest->file('profile_image');
            $finalPath = $file->store('profile_images', 'public');
            $user->update(['profile_image' => $finalPath]);
            \Log::info('プロフィール画像保存成功', ['user_id' => Auth::id(), 'path' => $finalPath]);
        }

        \Log::info('プロフィール更新完了', ['user_id' => Auth::id()]);
        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました');
    } catch (\Exception $e) {
        \Log::error('プロフィール更新エラー', ['error' => $e->getMessage()]);
        return redirect()->back()->withErrors(['message' => '予期しないエラーが発生しました']);
    }
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
