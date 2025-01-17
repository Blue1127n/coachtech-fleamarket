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
        \Log::info('Middleware started: RedirectIfProfileIncomplete', [
            'route_name' => $request->route() ? $request->route()->getName() : 'unknown',
            'user_authenticated' => Auth::check(),
            'user' => Auth::check() ? Auth::user()->only(['id', 'name', 'email', 'postal_code', 'address']) : null,
        ]);

        // 未認証のユーザーはスキップ
        if (!Auth::check()) {
            \Log::info('User not authenticated, skipping middleware');
            return $next($request);
        }

        $currentRoute = $this->getCurrentRouteName($request);

        // メール未認証の場合はスキップ
    if (!Auth::user()->hasVerifiedEmail()) {
        \Log::info('User email not verified, skipping middleware', ['user_id' => Auth::id()]);
        return $next($request);
    }

        // プロフィールが未完了の場合
        if ($this->isProfileIncomplete()) {
            \Log::info('Profile is incomplete, redirecting user', [
                'user_id' => Auth::id(),
                'current_route' => $currentRoute,
                'missing_fields' => [
                    'name' => empty(Auth::user()->name),
                    'postal_code' => empty(Auth::user()->postal_code),
                    'address' => empty(Auth::user()->address),
                ],
            ]);

            // プロフィール編集画面以外の場合
            if ($currentRoute !== 'mypage.profile') {
                \Log::info('Redirecting to profile setup page', ['user_id' => Auth::id()]);
                return redirect()->route('mypage.profile')->with('message', 'プロフィールを設定してください');
            }
        } elseif ($currentRoute === 'mypage.profile.update' && $request->isMethod('PUT')) {
            // プロフィール更新後に商品一覧にリダイレクト
            \Log::info('Profile updated, redirecting to products.index', ['user_id' => Auth::id()]);
            return redirect()->route('products.index')->with('success', 'プロフィールが更新されました');
        }

        \Log::info('Middleware passed successfully', ['user_id' => Auth::id()]);
        return $next($request);
    }

    /**
     * 現在のルート名を取得
     */
    private function getCurrentRouteName(Request $request): ?string
    {
        return $request->route() ? $request->route()->getName() : null;
    }

    /**
     * プロフィールが未完了かどうかをチェック
     */
    private function isProfileIncomplete(): bool
    {
        $user = Auth::user();
        return empty($user->name) || empty($user->postal_code) || empty($user->address);
    }
}