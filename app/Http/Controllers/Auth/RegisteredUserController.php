<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Services\MathCaptchaService;
use App\Services\SupabaseAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $captcha = app(MathCaptchaService::class)->generate();

        return view('auth.register', [
            'captcha_question' => $captcha['question'],
            'campuses'         => Campus::where('is_active', true)->orderBy('nama_kampus')->get(),
            'prodiMahasiswa'   => config('prodi.mahasiswa'),
            'prodiStaff'       => $this->prodiStaff(),
        ]);
    }

    public function store(Request $request, SupabaseAuth $supabase): RedirectResponse
    {
        $captchaService = app(MathCaptchaService::class);

        $request->validate([
            'role'       => ['required', 'in:mahasiswa,staff'],
            'id_campus'  => ['required', 'exists:campuses,id'],
            'name'       => ['required', 'string', 'max:255'],
            'nim'        => ['required_if:role,mahasiswa', 'nullable', 'string', 'max:20'],
            'nip'        => ['required_if:role,staff', 'nullable', 'string', 'max:30'],
            'prodi_unit' => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{8,13}$/'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', function ($attr, $value, $fail) {
                if (!SupabaseAuth::isBsiEmail($value)) {
                    $fail('Pendaftaran hanya dapat menggunakan email resmi BSI (@bsi.ac.id).');
                }
            }],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'captcha_answer' => ['required', 'integer', 'min:0', 'max:18'],
        ], [
            'nim.required_if'   => 'NIM wajib diisi untuk mahasiswa.',
            'nip.required_if'   => 'NIP/NIDN wajib diisi untuk staff/dosen.',
            'prodi_unit.required' => 'Pilih program studi / unit kerja.',
            'id_campus.required' => 'Pilih cabang kampus asal.',
            'phone.required'    => 'Nomor HP wajib diisi agar notifikasi WhatsApp bisa dikirim.',
            'phone.regex'       => 'Nomor HP harus nomor Indonesia yang valid, contoh: 081234567890.',
        ]);

        if (!$captchaService->validate((int) $request->captcha_answer)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'))
                ->withErrors(['captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.'])
                ->with(['captcha_question' => $captchaService->generate()['question']]);
        }

        $role = $request->role;

        $result = $supabase->signUp($request->email, $request->password, [
            'name'       => trim($request->name),
            'role'       => $role,
            'nim'        => $role === 'mahasiswa' ? trim($request->nim) : null,
            'nip'        => $role === 'staff' ? trim($request->nip) : null,
            'phone'      => trim($request->phone),
            'id_campus'  => (int) $request->id_campus,
            'prodi_unit' => $request->prodi_unit,
        ]);

        if (!$result['ok']) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'))
                ->withErrors(['email' => $result['message']])
                ->with(['captcha_question' => $captchaService->generate()['question']]);
        }

        // Lengkapi profil (trigger Supabase hanya mengisi data dasar).
        $authUser = $result['data']['user'] ?? $result['data'];
        if (!empty($authUser['id'])) {
            $profile = $supabase->profileFor($authUser);
            $profile->forceFill([
                'name'       => trim($request->name),
                'role'       => $role,
                'nim'        => $role === 'mahasiswa' ? trim($request->nim) : null,
                'nip'        => $role === 'staff' ? trim($request->nip) : null,
                'phone'      => trim($request->phone),
                'id_campus'  => (int) $request->id_campus,
                'prodi_unit' => $request->prodi_unit,
            ])->save();
        }

        session(['pending_verification_email' => strtolower(trim($request->email))]);

        return redirect()->route('verification.otp')
            ->with('status', 'Kode verifikasi telah dikirim ke ' . strtolower(trim($request->email)) . '.');
    }

    private function prodiStaff(): array
    {
        $groups = config('prodi.staff');
        foreach (config('prodi.mahasiswa') as $faculty => $list) {
            $groups['Dosen - ' . $faculty] = $list;
        }

        return $groups;
    }
}
