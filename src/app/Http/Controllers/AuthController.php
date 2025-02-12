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
        \Log::info('Login by email:', ['result' => $loginByEmail]);

        $loginByName = Auth::attempt(['name' => $credentials['email'], 'password' => $credentials['password']]);
        \Log::info('Login by name:', ['result' => $loginByName]);

        // ログイン失敗時の処理
       if (!$loginByEmail && !$loginByName) {
            \Log::error('Login failed: Invalid credentials', ['email' => $credentials['email']]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'ログイン情報が登録されていません'], 422);
            }

            return back()->withErrors(['login' => 'ログイン情報が登録されていません'])->withInput();
        }

        // メール認証済みかを確認
        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout(); // 認証を解除

            if ($request->expectsJson()) {
                return response()->json(['message' => __('auth.unverified_email')], 403);
            }

            return back()->withErrors(['login' => __('auth.unverified_email')])->withInput();
        }

        // 初回ログイン後のプロフィール設定リダイレクトフラグを設定
        if (is_null(Auth::user()->address) || empty(Auth::user()->postal_code)) { // 初回ログインかどうかを判定
            session(['redirect_to_profile' => true]);
        }

        // 認証成功時の処理
        $request->session()->regenerate(); // セッションの再生成
        \Log::info('Login successful for user', ['user' => Auth::user()->id]);

        // JSONリクエストの場合はJSONレスポンスを返す
        if ($request->expectsJson()) {
            return response()->json(['message' => 'ログインしました', 'user' => Auth::user()], 200);
        }

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

        \Log::info('Register Request Headers:', $request->headers->all());

        // ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // パスワードをハッシュ化
        ]);

        // 自動的にログイン
        Auth::login($user);

        // JSONリクエストの場合はJSONレスポンスを返す
        if ($request->expectsJson()) {
            return response()->json([
                'message' => '会員登録が完了しました',
                'user' => $user
            ], 201);
        }

        // **JSONリクエストでない場合のみメールを送信**
        $user->sendEmailVerificationNotification();

        // メール認証済みでない場合、認証画面へ
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 登録成功後、ログイン画面へリダイレクト
        return redirect()->route('products.mylist')->with('success', '会員登録が完了しました。マイリストをご確認ください。');
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout(); // ログアウト処理を実行

        // セッションの無効化と再生成
        $request->session()->invalidate(); // セッションを無効化（セッションデータを削除）
        $request->session()->regenerateToken(); // CSRFトークンを再生成

        // 商品一覧画面（トップページ）にリダイレクト
        return redirect('/')->with('success', 'ログアウトしました');
    }
}
