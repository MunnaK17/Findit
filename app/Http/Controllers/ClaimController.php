<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Report;
use App\Models\User;
use App\Events\ClaimSubmittedEvent;
use App\Events\ClaimStatusEvent;
use App\Notifications\ClaimSubmittedNotification;
use App\Notifications\ClaimStatusNotification;
use App\Services\MathCaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ClaimController extends Controller
{
    // Form ajukan klaim
    public function create($reportId)
    {
        $report = Report::where('jenis_laporan', 'temuan')
            ->where('status', 'approved')
            ->findOrFail($reportId);

        // Cek apakah user sudah pernah klaim laporan ini
        $sudahKlaim = Claim::where('id_report', $reportId)
            ->where('id_user', Auth::id())
            ->exists();

        if ($sudahKlaim) {
            return redirect()->route('reports.show', $reportId)
                ->with('error', 'Kamu sudah pernah mengajukan klaim untuk barang ini.');
        }

        // User tidak boleh klaim laporan miliknya sendiri
        if ($report->id_user === Auth::id()) {
            return redirect()->route('reports.show', $reportId)
                ->with('error', 'Kamu tidak bisa mengklaim laporan milikmu sendiri.');
        }

        // Generate captcha
        $captchaService = app(MathCaptchaService::class);
        $captcha = $captchaService->generate();

        return view('claims.create', [
            'report' => $report,
            'captcha_question' => $captcha['question'],
        ]);
    }

    // Simpan klaim
    public function store(Request $request, $reportId)
    {
        $captchaService = app(MathCaptchaService::class);

        $report = Report::where('jenis_laporan', 'temuan')
            ->where('status', 'approved')
            ->findOrFail($reportId);

        // Double-check: cek duplikasi lagi
        $sudahKlaim = Claim::where('id_report', $reportId)
            ->where('id_user', Auth::id())
            ->where('status_klaim', '!=', 'rejected')
            ->exists();

        if ($sudahKlaim) {
            return redirect()->route('reports.show', $reportId)
                ->with('error', 'Kamu sudah pernah mengajukan klaim untuk barang ini.');
        }

        // User tidak boleh klaim laporan miliknya sendiri
        if ($report->id_user === Auth::id()) {
            return redirect()->route('reports.show', $reportId)
                ->with('error', 'Kamu tidak bisa mengklaim laporan milikmu sendiri.');
        }

        $request->validate([
            'pesan_klaim' => ['required', 'string', 'min:20', 'max:1000'],
            'captcha_answer' => ['required', 'integer', 'min:0', 'max:18'],
        ]);

        // Validate captcha
        if (!$captchaService->validate((int) $request->captcha_answer)) {
            return back()
                ->withInput($request->except('captcha_answer'))
                ->withErrors(['captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.'])
                ->with(['captcha_question' => $captchaService->generate()['question']]);
        }

        $claim = Claim::create([
            'id_report'    => $reportId,
            'id_user'      => Auth::id(),
            'pesan_klaim'  => $request->pesan_klaim,
            'status_klaim' => 'pending',
            'tanggal_klaim' => now()->toDateString(),
        ]);

        // Load relasi
        $claim->load('user', 'report');

        // Notifikasi ke semua admin (database + broadcast realtime)
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new ClaimSubmittedNotification($claim, $claim->report));
        ClaimSubmittedEvent::dispatch($claim, $claim->report);

        // Notifikasi ke user (email + WA) — langsung sync, tanpa queue
        try {
            $claim->user->notify(new ClaimStatusNotification($claim, $claim->report, 'pending'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim ClaimStatusNotification: ' . $e->getMessage());
        }

        return redirect()->route('my.claims')
            ->with('success', 'Klaim berhasil diajukan! Menunggu verifikasi admin.');
    }

    // Klaim milik user yang login
    public function myClaims()
    {
        $claims = Claim::with(['report.category'])
            ->where('id_user', Auth::id())
            ->latest()
            ->paginate(10);

        return view('claims.my-claims', compact('claims'));
    }

    // Cancel klaim (hanya jika pending)
    public function cancel($id)
    {
        $claim = Claim::where('id_user', Auth::id())->findOrFail($id);

        if ($claim->status_klaim !== 'pending') {
            return redirect()->route('my.claims')
                ->with('error', 'Hanya klaim yang pending yang bisa dibatalkan.');
        }

        $claim->delete();

        return redirect()->route('my.claims')
            ->with('success', 'Klaim berhasil dibatalkan.');
    }
}