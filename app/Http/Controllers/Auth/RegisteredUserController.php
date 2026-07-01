<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MathCaptchaService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $captchaService = app(MathCaptchaService::class);
        $captcha = $captchaService->generate();

        return view('auth.register', [
            'captcha_question' => $captcha['question'],
        ]);
    }

    // Pastikan store() hanya ADA SATU di sini
    public function store(Request $request): RedirectResponse
    {
        $captchaService = app(MathCaptchaService::class);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nim'      => ['nullable', 'string', 'max:20'],
            'phone'    => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{8,13}$/'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'captcha_answer' => ['required', 'integer', 'min:0', 'max:18'],
        ], [
            'phone.required' => 'Nomor HP wajib diisi agar notifikasi WhatsApp bisa dikirim.',
            'phone.regex' => 'Nomor HP harus nomor Indonesia yang valid, contoh: 081234567890 atau 6281234567890.',
        ]);

        // Validate captcha
        if (!$captchaService->validate((int) $request->captcha_answer)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'))
                ->withErrors(['captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.'])
                ->with(['captcha_question' => $captchaService->generate()['question']]);
        }

        $user = User::create([
            'name'     => $request->name,
            'nim'      => $request->nim,
            'phone'    => $request->string('phone')->trim()->toString(),
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'mahasiswa',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
