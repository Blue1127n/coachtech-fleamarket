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

        if (!Auth::check()) {
            \Log::info('User not authenticated, skipping middleware');
            return $next($request);
        }

        if ($request->route()->getName() === 'logout') {
            \Log::info('Skipping middleware for logout');
            return $next($request);
        }

        if ($request->route()->getName() === 'mypage.profile.update') {
            \Log::info('Skipping middleware for profile update');
            return $next($request);
        }

        $currentRoute = $this->getCurrentRouteName($request);

    if (!Auth::user()->hasVerifiedEmail()) {
        \Log::info('User email not verified, skipping middleware', ['user_id' => Auth::id()]);
        return $next($request);
    }

        if ($this->isProfileIncomplete()) {

            if ($currentRoute !== 'mypage.profile') {

                return redirect()->route('mypage.profile')->with('message', 'プロフィールを設定してください');
            }
        } elseif ($currentRoute === 'mypage.profile.update' && $request->isMethod('PUT')) {

            return redirect()->route('products.index')->with('success', 'プロフィールが更新されました');
        }

        return $next($request);
    }

    private function getCurrentRouteName(Request $request): ?string
    {
        return $request->route() ? $request->route()->getName() : null;
    }

    private function isProfileIncomplete(): bool
    {
        $user = Auth::user();
        return empty($user->name) || empty($user->postal_code) || empty($user->address);
    }
}