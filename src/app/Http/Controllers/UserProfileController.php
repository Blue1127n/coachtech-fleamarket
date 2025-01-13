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

        return view('profile.show', compact('user', 'items', 'page'));
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
    // リクエストメソッドとCSRFトークンを確認
    \Log::info('リクエストメソッド確認', [
        'method' => request()->method(),
        'expected' => 'PUT',
    ]);
    \Log::info('CSRFトークン', [
        'csrf_token' => request()->header('X-CSRF-TOKEN'),
    ]);

    \Log::info('Updateメソッドに到達しました - リクエスト内容', [
        'method' => request()->method(),
        'url' => request()->url(),
        'input' => request()->all(),
        'files' => request()->file(),
    ]);

    // バリデーションエラーがセッションに存在する場合、ログに記録
    if (session()->has('errors')) {
        \Log::error('セッションにバリデーションエラーが存在します', ['errors' => session('errors')->all()]);
    }

    try {
        // バリデーション処理開始
        \Log::info('Validation処理を開始します');
        $validatedAddress = $addressRequest->validated();
        $validatedProfile = $profileRequest->validated();

        \Log::info('Validationが正常に完了しました', [
            'validatedAddress' => $validatedAddress,
            'validatedProfile' => $validatedProfile,
        ]);

        $user = auth()->user();

        // プロフィール画像のアップロード処理
        if ($profileRequest->hasFile('profile_image')) {
            \Log::info('プロフィール画像のアップロードを検出しました');
            $file = $profileRequest->file('profile_image');
            if ($file->isValid()) {
                $tempPath = $file->store('temp', 'public');
                \Log::info('一時保存されたプロフィール画像', ['path' => $tempPath]);
                session(['profile_image_temp' => $tempPath]);
            } else {
                \Log::error('無効なプロフィール画像がアップロードされました');
                return redirect()->back()->withErrors(['profile_image' => '無効なファイルがアップロードされました']);
            }
        }

        // ユーザー情報の更新
        \Log::info('ユーザー情報を更新します');
        $user->update([
            'name' => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address' => $validatedAddress['address'],
            'building' => $validatedAddress['building'] ?? null,
        ]);

        // プロフィール画像の永続保存
        if (session()->has('profile_image_temp')) {
            $tempPath = session('profile_image_temp');
            $finalPath = str_replace('temp/', 'profile_images/', $tempPath);

            if (\Storage::disk('public')->exists($tempPath)) {
                \Storage::disk('public')->move($tempPath, $finalPath);
                $user->profile_image = str_replace('public/', '', $finalPath);
                $user->save();
                \Log::info('プロフィール画像が保存されました', ['finalPath' => $finalPath]);
                session()->forget('profile_image_temp');
            } else {
                \Log::warning('一時保存画像が存在しません', ['path' => $tempPath]);
            }
        }

        \Log::info('プロフィール更新が正常に完了しました');
        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました');
    } catch (\Exception $e) {
        \Log::error('エラーが発生しました: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
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
