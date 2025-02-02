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

    // 購入した商品か出品した商品を取得
    $items = $page === 'sell' ? $user->items : $user->purchasedItems;

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
    \Log::info('ProfileRequest ファイル情報', ['files' => $profileRequest->file()]);

    try {
        // AddressRequest のバリデーション処理
        $validatedAddress = $addressRequest->validated();

        \Log::info('AddressRequest バリデーション結果', [
            'validated_address' => $validatedAddress,
        ]);

        // ProfileRequest のバリデーション処理
        try {
            $validatedProfile = $profileRequest->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ProfileRequest バリデーションエラー時の処理
            \Log::error('プロフィール画像のバリデーションエラー', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        \Log::info('ProfileRequest バリデーション結果', [
            'validated_profile' => $validatedProfile,
        ]);

        // ユーザー情報更新
        $user = auth()->user();
        $user->update([
            'name' => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address' => $validatedAddress['address'],
            'building' => $validatedAddress['building'] ?? null,
        ]);

        \Log::info('データベース保存結果', ['user_data' => $user->only(['name', 'postal_code', 'address', 'building'])]);

        // 画像アップロード
        if ($profileRequest->hasFile('profile_image')) {
            \Log::info('画像アップロード処理開始');
            try {
            // ファイル取得
            $file = $profileRequest->file('profile_image');
            // ファイル保存
            $finalPath = $file->store('profile_images', 'public');
            $user->update(['profile_image' => $finalPath]);
            // 成功ログ
            \Log::info('プロフィール画像保存成功', ['path' => $finalPath]);
        } catch (\Exception $imageException) {
            // エラーログ
            \Log::error('プロフィール画像保存エラー', [
                'error' => $imageException->getMessage(), // エラー内容
                'file' => $file->getClientOriginalName(), // アップロードされたファイル名
            ]);
            // ユーザーへのエラー通知
            return redirect()->back()->withErrors(['profile_image' => '画像アップロードに失敗しました']);
        }
    } else {
        \Log::info('プロフィール画像はアップロードされていません');
    }

    // プロフィール更新後、商品一覧画面にリダイレクト
    return redirect()->route('products.index')->with('success', 'プロフィールが更新されました');
} catch (\Exception $e) {
    // 予期しないエラーが発生した場合
    \Log::error('プロフィール更新エラー', ['error' => $e->getMessage()]);

    return redirect()->back()->withErrors(['message' => '予期しないエラーが発生しました']);
}
}

    // 購入した商品一覧
    public function purchasedItems()
{
    $user = Auth::user();
    $items = $user->purchasedItems()->get();

    return view('profile.purchased', compact('items'));
}

    // 出品した商品一覧
    public function soldItems()
    {
        $items = Auth::user()->items()->where('status_id', Status::SOLD)->get();
        return view('profile.sold', compact('items')); // 出品商品一覧ビュー
    }
}
