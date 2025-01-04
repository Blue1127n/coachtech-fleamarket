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

    // 1. 会員登録直後のリダイレクトフラグをチェック
    if (session('redirect_to_profile', false)) {
        session()->forget('redirect_to_profile'); // フラグを削除
        return redirect()->route('profile.edit')->with('success', 'プロフィールを設定してください');
    }

    // 2. ログインしている場合で、プロフィール情報が未設定の場合
    if (Auth::check() && empty(Auth::user()->address)) {
        return redirect()->route('profile.edit')->with('message', 'プロフィールを設定してください。');
    }

        return $next($request);
    }
}
