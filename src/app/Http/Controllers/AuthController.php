<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // ログイン画面を表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン処理
    public function login(LoginRequest $request)
    {
        // 入力されたメールアドレスとパスワードを取得して認証を試みる
        if (!Auth::attempt($request->only('email', 'password'))) {
            // 認証失敗時のエラーを返す
            return back()->withErrors([
                'login' => 'ログイン情報が登録されていません',
            ])->withInput();
        }

        // メール認証済みかを確認
        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout(); // 認証を解除
            return back()->withErrors([
                'login' => 'ログイン情報が登録されていません',
            ]);
        }

        // 認証成功時の処理
        $request->session()->regenerate(); // セッションの再生成

        return redirect()->intended('/')->with('success', 'ログインしました');
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout(); // ログアウト

        // セッションの無効化と再生成
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'ログアウトしました');
    }
}
