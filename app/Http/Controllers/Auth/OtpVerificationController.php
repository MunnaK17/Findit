<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

/**
 * Verifikasi email dengan kode OTP dari Supabase Auth.
 */
class OtpVerificationController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $email = session('pending_verification_email');
        if (!$email) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    public function verify(Request $request, SupabaseAuth $supabase): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits_between:6,8']], [
            'code.required' => 'Masukkan kode verifikasi dari email Anda.',
            'code.digits_between' => 'Kode verifikasi terdiri dari angka (6-8 digit).',
        ]);

        $email = session('pending_verification_email');
        if (!$email) {
            return redirect()->route('login');
        }

        $key = 'otp-verify:' . $email;
        if (RateLimiter::tooManyAttempts($key, 8)) {
            return back()->withErrors(['code' => 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.']);
        }

        $result = $supabase->verifyOtp($email, $request->code);

        if (!$result['ok']) {
            RateLimiter::hit($key, 300);

            return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa. Minta kode baru bila perlu.']);
        }

        RateLimiter::clear($key);

        $authUser = $result['data']['user'] ?? null;
        if (!$authUser || empty($authUser['id'])) {
            return redirect()->route('login')->with('status', 'Email terverifikasi. Silakan masuk.');
        }

        $user = $supabase->profileFor($authUser);
        Auth::login($user);
        $supabase->rememberSession($result['data']);
        $request->session()->forget('pending_verification_email');
        $request->session()->regenerate();

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'dashboard')
            ->with('success', 'Akun berhasil diaktifkan. Selamat datang di FindIT!');
    }

    public function resend(Request $request, SupabaseAuth $supabase): RedirectResponse
    {
        $email = session('pending_verification_email');
        if (!$email) {
            return redirect()->route('login');
        }

        $key = 'otp-resend:' . $email;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return back()->withErrors(['code' => 'Tunggu ' . RateLimiter::availableIn($key) . ' detik sebelum meminta kode lagi.']);
        }
        RateLimiter::hit($key, 60);

        $result = $supabase->resend($email);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['message']]);
        }

        return back()->with('status', 'Kode baru telah dikirim ke ' . $email . '.');
    }
}
