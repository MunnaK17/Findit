<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private const EMAIL_RULE = '/^[^@\s]+@([a-z0-9-]+\.)*bsi\.ac\.id$/i';

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:' . self::EMAIL_RULE],
            'password'    => 'required|string|min:8|confirmed',
            'role'        => 'nullable|in:mahasiswa,staff',
            'nim'         => 'nullable|string|max:20',
            'nip'         => 'nullable|string|max:30',
            'phone'       => 'nullable|string|max:20',
            'id_campus'   => 'nullable|exists:campuses,id',
            'prodi_unit'  => 'nullable|string|max:100',
        ], [
            'email.regex' => 'Pendaftaran hanya dapat menggunakan email resmi BSI (@bsi.ac.id).',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $role = $validated['role'] ?? 'mahasiswa';

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => strtolower($validated['email']),
            'password'   => Hash::make($validated['password']),
            'nim'        => $role === 'mahasiswa' ? ($validated['nim'] ?? null) : null,
            'nip'        => $role === 'staff' ? ($validated['nip'] ?? null) : null,
            'phone'      => $validated['phone'] ?? null,
            'role'       => $role,
            'id_campus'  => $validated['id_campus'] ?? null,
            'prodi_unit' => $validated['prodi_unit'] ?? null,
        ]);

        $this->sendVerificationCode($user);

        return response()->json([
            'success' => true,
            'requires_verification' => true,
            'email'   => $user->email,
            'message' => 'Registrasi berhasil. Kode verifikasi telah dikirim ke email Anda.',
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah terverifikasi, silakan login.'], 422);
        }

        if (! $user->verification_code
            || ! $user->verification_expires_at
            || $user->verification_expires_at->isPast()
            || ! hash_equals($user->verification_code, hash('sha256', trim($request->code)))) {
            return response()->json(['success' => false, 'message' => 'Kode verifikasi salah atau sudah kedaluwarsa.'], 422);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_expires_at' => null,
        ])->save();

        $user->load('campus');
        $token = $user->createToken('findit_mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', strtolower($request->email))->first();

        if ($user && ! $user->email_verified_at) {
            $this->sendVerificationCode($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jika akun ditemukan, kode verifikasi baru telah dikirim.',
        ]);
    }

    private function sendVerificationCode(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'verification_code' => hash('sha256', $code),
            'verification_expires_at' => now()->addMinutes(15),
        ])->save();

        try {
            Mail::raw(
                "Halo {$user->name},\n\nKode verifikasi akun FindIT Anda: {$code}\n\nKode berlaku selama 15 menit. Abaikan email ini jika Anda tidak mendaftar.",
                function ($m) use ($user) {
                    $m->to($user->email)->subject('Kode Verifikasi Akun FindIT');
                }
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi tidak cocok.',
            ], 401);
        }

        if (! $user->email_verified_at) {
            $this->sendVerificationCode($user);

            return response()->json([
                'success' => false,
                'needs_verification' => true,
                'email'   => $user->email,
                'message' => 'Email belum diverifikasi. Kode verifikasi baru telah dikirim.',
            ], 403);
        }

        $user->load('campus');
        $token = $user->createToken('findit_mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => $user,
        ]);
    }
    public function user(Request $request)
    {
        return response()->json($request->user()->load('campus'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}

