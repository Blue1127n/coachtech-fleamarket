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

    if ($this->isProfileIncomplete()) {
        \Log::info('Profile is incomplete', [
            'missing_fields' => [
                'name' => empty(Auth::user()->name),
                'postal_code' => empty(Auth::user()->postal_code),
                'address' => empty(Auth::user()->address),
            ],
        ]);

        if (in_array($currentRoute, ['mypage.profile', 'mypage.profile.update'], true)) {
            \Log::info('Skipping redirect for profile-related routes', ['route' => $currentRoute]);
            return $next($request);
        }

        return redirect()->route('mypage.profile')->with('message', 'プロフィールを設定してください');
    }

    \Log::info('Middleware passed successfully');
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