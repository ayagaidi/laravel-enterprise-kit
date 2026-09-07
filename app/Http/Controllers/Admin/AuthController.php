<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.login');
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $key = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => __('app.too_many_attempts')])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['email' => __('app.invalid_credentials')])->onlyInput('email');
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => __('app.account_disabled')]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $audit->record('auth.login');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request, AuditService $audit): RedirectResponse
    {
        $audit->record('auth.logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
