<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ログイン画面を表示
    public function showLoginForm()
    {
        \Log::info('Rendering login form.');
        return view('auth.login');
    }

    // ログイン処理
    public function login(LoginRequest $request)
    {
        \Log::info('Login request received', $request->all());

        $credentials = $request->only('email', 'password');

        \Log::info('Attempting login with credentials', $credentials);

        // ユーザー名またはメールアドレスでログインを試みる
        $loginByEmail = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']]);
        $loginByName = Auth::attempt(['name' => $credentials['email'], 'password' => $credentials['password']]);

        if (!$loginByEmail && !$loginByName) {
            \Log::error('Login failed for credentials', $credentials);
            return back()->withErrors(['login' => 'ログイン情報が登録されていません'])->withInput();
        }

        // メール認証済みかを確認
        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout(); // 認証を解除
            return back()->withErrors(['login' => __('auth.unverified_email')])->withInput();
        }

        // 初回ログイン後のプロフィール設定リダイレクトフラグを設定
        if (is_null(Auth::user()->address)) { // 初回ログインかどうかを判定
            session(['redirect_to_profile' => true]);
        }

        // 認証成功時の処理
        $request->session()->regenerate(); // セッションの再生成
        \Log::info('Login successful for user', ['user' => Auth::user()]);

        // プロフィール設定が必要ならプロフィール画面にリダイレクト
        if (session('redirect_to_profile', false)) {
            session()->forget('redirect_to_profile');
            return redirect()->route('mypage.profile')->with('success', 'プロフィールを設定してください');
        }

        return redirect()->intended(route('products.index'))->with('success', 'ログインしました');
    }

    // 会員登録画面の表示
    public function showRegisterForm()
    {
        return view('auth.register'); // 登録画面のビューを表示
    }

    // 会員登録処理
    public function register(RegisterRequest $request)
    {
        // ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // パスワードをハッシュ化
        ]);

        // 自動的にログイン
        Auth::login($user);

        // デバッグ: メール送信トリガーをログに記録
        \Log::info('Sending verification email to ' . $user->email);

        // 認証メールを送信
        $user->sendEmailVerificationNotification();

        // ユーザーがメール認証済みでない場合、/email/verify にリダイレクト
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 登録成功後、ログイン画面へリダイレクト
        return redirect()->route('products.mylist')->with('success', '会員登録が完了しました。マイリストをご確認ください。');
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
