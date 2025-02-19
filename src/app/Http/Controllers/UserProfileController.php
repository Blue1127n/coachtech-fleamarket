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

    public function show(Request $request)
{
    $user = Auth::user();
    $page = $request->get('page', 'sell');

    $items = $page === 'sell' ? $user->items : $user->purchasedItems;

    return view('profile.show', compact('user', 'items', 'page'));
}


    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }


    public function update(Request $request, AddressRequest $addressRequest, ProfileRequest $profileRequest)
{

    \Log::info('Request Data:', $request->all());
    try {
        // バリデーション
        $validatedAddress = $addressRequest->validated();

        try {
            $validatedProfile = $profileRequest->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = auth()->user();
        $user->update([
            'name' => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address' => $validatedAddress['address'],
            'building' => $validatedAddress['building'] ?? null,
        ]);

        if ($profileRequest->hasFile('profile_image')) {

            try {

            $file = $profileRequest->file('profile_image');

            $finalPath = $file->store('profile_images', 'public');
            $user->update(['profile_image' => $finalPath]);

        } catch (\Exception $imageException) {

            return response()->json(['errors' => ['profile_image' => '画像アップロードに失敗しました']], 422);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'プロフィールが更新されました',
        'redirect_url' => route('mypage.profile') // プロフィール画面へリダイレクト
    ]);

} catch (\Exception $e) {
    return response()->json([
        'message' => '予期しないエラーが発生しました',
        'error' => $e->getMessage()
    ], 500);
}
}

    public function purchasedItems()
{
    $user = Auth::user();
    $items = $user->purchasedItems()->get();

    return view('profile.purchased', compact('items'));
}

    public function soldItems()
    {
        $items = Auth::user()->items()->where('status_id', Status::SOLD)->get();
        return view('profile.sold', compact('items'));
    }
}
