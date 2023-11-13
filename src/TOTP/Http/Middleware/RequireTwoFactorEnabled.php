<?php

namespace YanGusik\TwoFactor\TOTP\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use YanGusik\TwoFactor\TOTP\Contracts\TwoFactorAuthenticatable;

class RequireTwoFactorEnabled
{
    public function handle(Request $request, Closure $next, string $route = '2fa.notice'): mixed
    {
        $user = $request->user();

        if (!$user instanceof TwoFactorAuthenticatable || $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => trans('two_factor::messages.enable')], 403)
            : response()->redirectToRoute($route);
    }
}