<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('claims.create', compact('report'));
    }

    // Simpan klaim
    public function store(Request $request, $reportId)
    {
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
        ]);

        Claim::create([
            'id_report'    => $reportId,
            'id_user'      => Auth::id(),
            'pesan_klaim'  => $request->pesan_klaim,
            'status_klaim' => 'pending',
            'tanggal_klaim' => now()->toDateString(),
        ]);

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