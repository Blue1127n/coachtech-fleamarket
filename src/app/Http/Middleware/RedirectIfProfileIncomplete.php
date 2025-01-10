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
    // ミドルウェアが開始されたことをログに記録
    \Log::info('RedirectIfProfileIncomplete middleware started', [
        'route' => $request->route() ? $request->route()->getName() : 'unknown',
        'user' => Auth::check() ? Auth::user()->toArray() : 'guest',
    ]);

    if (!Auth::check()) {
        \Log::info('User is not authenticated');
        return $next($request);
    }

    $currentRoute = $this->getCurrentRouteName($request);
    \Log::info('Current route', ['route' => $currentRoute]);

    // `mypage.profile.update` を例外として除外
    if (in_array($currentRoute, ['mypage.profile.update'])) {
        \Log::info('Skipping middleware for route', ['route' => $currentRoute]);
        return $next($request);
    }

    if ($this->shouldRedirectToProfileSetup() && $currentRoute !== 'mypage.profile') {
        \Log::info('Redirecting to profile setup');
        return redirect()->route('mypage.profile')->with('message', 'プロフィールを設定してください');
    }

    if ($this->isProfileIncomplete() && $currentRoute !== 'mypage.profile') {
        \Log::info('Redirecting due to incomplete profile', [
            'incomplete_fields' => [
                'name' => empty(Auth::user()->name),
                'postal_code' => empty(Auth::user()->postal_code),
                'address' => empty(Auth::user()->address),
            ],
        ]);
        return redirect()->route('mypage.profile')->with('message', 'プロフィールを設定してください');
    }

    \Log::info('Middleware passed successfully');
    return $next($request);

}

private function getCurrentRouteName(Request $request): ?string
{
    return $request->route() ? $request->route()->getName() : null;
}

private function shouldRedirectToProfileSetup(): bool
{
    if (session('redirect_to_profile', false)) {
        session()->forget('redirect_to_profile');
        return true;
    }
    return false;
}

private function isProfileIncomplete(): bool
{
    $user = Auth::user();
    return empty($user->address) || empty($user->postal_code) || empty($user->name);
}
}
