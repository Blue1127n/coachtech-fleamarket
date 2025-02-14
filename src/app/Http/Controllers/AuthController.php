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

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(LoginRequest $request)
{

    \Log::info('Login request received', ['request_data' => $request->all()]);

    \Log::info('Request expects JSON?', ['expectsJson' => $request->expectsJson()]);

    $credentials = $request->only('email', 'password');

    $user = User::where('email', $credentials['email'])
                ->orWhere('name', $credentials['email'])
                ->first();

    if (!$user || !Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {
        \Log::error('Login failed: Invalid credentials', ['email' => $credentials['email']]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'ログインに失敗しました',
                'errors' => ['email' => ['認証情報が正しくありません。']]
            ], 422);
        }

        return redirect()->back()->withErrors(['login' => 'ログイン情報が登録されていません'])->withInput();
    }

    if (!$user->hasVerifiedEmail()) {
        Auth::logout();

        if ($request->expectsJson()) {
            return response()->json(['error' => 'メール認証が完了していません'], 403);
        }
        return back()->withErrors(['login' => __('auth.unverified_email')])->withInput();
    }

    if (is_null(Auth::user()->address) || empty(Auth::user()->postal_code)) {
        session(['redirect_to_profile' => true]);
    }

    \Log::info('Login successful for user', ['user' => Auth::user()]);

    $request->session()->regenerate();

    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'ログイン成功',
            'user' => Auth::user()
        ], 200);
    }

    if (session('redirect_to_profile', false)) {
        session()->forget('redirect_to_profile');
        return redirect()->route('mypage.profile')->with('success', 'プロフィールを設定してください');
    }

    return redirect()->intended(route('products.index'))->with('success', 'ログインしました');
}

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => '会員登録が完了しました',
                'user' => $user
            ], 201);
        }

        $user->sendEmailVerificationNotification();

        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->route('products.mylist')->with('success', '会員登録が完了しました。マイリストをご確認ください。');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'ログアウトしました');
    }
}
