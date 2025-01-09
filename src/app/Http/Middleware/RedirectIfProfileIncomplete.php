<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfProfileIncomplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // ログインしていない場合、このミドルウェアは適用されない
        if (!Auth::check()) {
            return $next($request);
        }

        // 現在のルート名を取得
    $currentRoute = $request->route()->getName();

    \Log::info('Current route:', ['route' => $currentRoute]); // 現在のルートをログ
    \Log::info('User data:', ['user' => Auth::user()]); // 現在のユーザー情報をログ

    // 1. 会員登録直後のリダイレクトフラグをチェック
    if (session('redirect_to_profile', false)) {
        \Log::info('Redirecting to profile setup');
        session()->forget('redirect_to_profile'); // フラグを削除

        // 現在のルートがプロフィール画面でない場合のみリダイレクト
        if ($currentRoute !== 'mypage.profile') {
        return redirect()->route('mypage.profile')->with('success', 'プロフィールを設定してください');
    }
}

    // 2. ログインしている場合で、プロフィール情報が未設定の場合
    if (Auth::check() && empty(Auth::user()->address)) {
        \Log::info('Redirecting due to missing profile address'); // ログで確認

        // 現在のルートがプロフィール編集ページでない場合のみリダイレクト
        if ($currentRoute !== 'mypage.profile') {
        return redirect()->route('mypage.profile')->with('message', 'プロフィールを設定してください');
    }
}

        return $next($request);
    }
}
