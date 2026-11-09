<?php

namespace App\Http\Controllers\Admin;

use App\Events\ClaimStatusEvent;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Verifikasi klaim. Email & notifikasi in-app ke pengklaim (dan ke pelapor saat laporan selesai)
 * dikirim trigger Supabase setiap status berubah, jadi controller ini tidak mengirim email sendiri.
 */
class ClaimController extends Controller
{
    // Daftar semua klaim
    public function index(Request $request)
    {
        $query = Claim::with(['user', 'report.category']);

        if ($request->status) {
            $query->where('status_klaim', $request->status);
        }

        $claims = $query->latest()->paginate(15)->withQueryString();

        return view('admin.claims.index', compact('claims'));
    }

    // Detail klaim
    public function show($id)
    {
        $claim = Claim::with(['user', 'report.category', 'report.user'])->findOrFail($id);
        return view('admin.claims.show', compact('claim'));
    }

    // Approve klaim → laporan selesai, klaim lain yang masih pending ditolak
    public function approve($id)
    {
        $claim = null;
        $error = DB::transaction(function () use ($id, &$claim) {
            // Kunci laporan dulu agar dua admin tidak bisa menyetujui dua klaim berbeda bersamaan.
            $reportId = Claim::whereKey($id)->value('id_report');
            abort_if($reportId === null, 404);
            $report = Report::whereKey($reportId)->lockForUpdate()->firstOrFail();
            $claim = Claim::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($claim->status_klaim !== 'pending') {
                return 'Klaim ini sudah diproses sebelumnya.';
            }
            if ($report->status !== 'approved') {
                return 'Laporan ini sudah selesai atau tidak aktif, klaim tidak bisa disetujui.';
            }

            $claim->update(['status_klaim' => 'approved']);
            $report->update(['status' => 'completed']);

            Claim::where('id_report', $claim->id_report)
                ->where('id', '!=', $claim->id)
                ->where('status_klaim', 'pending')
                ->update(['status_klaim' => 'rejected']);

            return null;
        });

        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        $claim->load('user', 'report');
        ClaimStatusEvent::dispatch($claim, $claim->report, 'approved');

        return redirect()->back()
            ->with('success', 'Klaim disetujui. Laporan otomatis ditandai selesai.');
    }

    // Reject klaim
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $claim = Claim::with('user', 'report')->findOrFail($id);

        if ($claim->status_klaim !== 'pending') {
            return redirect()->back()->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        // catatan_admin ikut dikirim ke pengklaim lewat email trigger Supabase.
        $claim->update([
            'status_klaim'  => 'rejected',
            'catatan_admin' => $request->input('admin_note'),
        ]);

        ClaimStatusEvent::dispatch($claim, $claim->report, 'rejected');

        return redirect()->back()
            ->with('success', 'Klaim berhasil di-reject.');
    }
}
