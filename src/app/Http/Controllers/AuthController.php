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

    // **リクエストの全データをログに記録**
    \Log::info('Login request received', ['request_data' => $request->all()]);

    // **リクエストの型をログに記録**
    \Log::info('Request Type', [
        'ajax' => $request->ajax(),
        'expectsJson' => $request->expectsJson(),
        'wantsJson' => $request->wantsJson(),
    ]);

    $credentials = $request->only('email', 'password');

    $user = User::where('email', $credentials['email'])
                ->orWhere('name', $credentials['email'])
                ->first();

    if (!$user || !Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {
        \Log::error('Login failed: Invalid credentials', ['email' => $credentials['email']]);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'ログインに失敗しました',
                'errors' => ['email' => ['認証情報が正しくありません。']]
            ], 422);
        }

        \Log::info('Redirecting back with login error');
        return redirect()->back()->withErrors(['login' => 'ログイン情報が登録されていません'])->withInput();
    }

    if (!$user->hasVerifiedEmail()) {
        \Log::warning('User has not verified email', ['user_id' => $user->id]);
        Auth::logout();

        if ($request->ajax()) {
            return response()->json(['error' => 'メール認証が完了していません'], 403);
        }
        return back()->withErrors(['login' => __('auth.unverified_email')])->withInput();
    }

    if (is_null(Auth::user()->address) || empty(Auth::user()->postal_code)) {
        \Log::info('User needs to set up profile', ['user_id' => Auth::id()]);
        session(['redirect_to_profile' => true]);
    }

    \Log::info('Login successful', ['user_id' => Auth::id()]);

    $request->session()->regenerate();

    if ($request->ajax()) {
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
        \Log::info('Logging out user', ['user_id' => Auth::id()]);

        Auth::logout();

        \Log::info('User logged out. Invalidating session...');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \Log::info('Redirecting to /');
        return redirect('/')->with('success', 'ログアウトしました');
    }
}
