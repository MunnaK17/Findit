<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\MathCaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $captchaService = app(MathCaptchaService::class);
        $captcha = $captchaService->generate();

        return view('auth.login', [
            'captcha_question' => $captcha['question'],
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $captchaService = app(MathCaptchaService::class);

        try {
            $request->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Regenerate captcha on validation error
            $captcha = $captchaService->generate();

            return back()
                ->withErrors($e->errors())
                ->withInput($request->except('password'))
                ->with(['captcha_question' => $captcha['question']]);
        }

        $request->session()->regenerate();

        // Redirect berdasarkan role
        if (auth()->user()->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
