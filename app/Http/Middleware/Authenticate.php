<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        if (! auth()->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();

            return $request->expectsJson()
                ? response()->json(['message' => 'Account disabled.'], 403)
                : redirect()->route('login')->withErrors(['email' => __('app.account_disabled')]);
        }

        return $next($request);
    }
}
