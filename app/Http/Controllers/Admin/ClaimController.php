<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use App\Models\User;
use App\Events\ClaimStatusEvent;
use App\Notifications\ClaimStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ClaimController extends Controller
{
    // Daftar semua klaim
    public function index(Request $request)
    {
        $query = Claim::with(['user', 'report.category']);

        if ($request->status) {
            $query->where('status_klaim', $request->status);
        }

        $claims = $query->latest()->paginate(15);

        return view('admin.claims.index', compact('claims'));
    }

    // Detail klaim
    public function show($id)
    {
        $claim = Claim::with(['user', 'report.category', 'report.user'])->findOrFail($id);
        return view('admin.claims.show', compact('claim'));
    }

    // Approve klaim → otomatis set report jadi completed
    public function approve($id)
    {
        $claim = Claim::with('user', 'report')->findOrFail($id);

        $claim->update(['status_klaim' => 'approved']);

        // Otomatis update status laporan jadi completed
        $claim->report->update(['status' => 'completed']);

        // Reject semua klaim lain untuk laporan yang sama
        Claim::where('id_report', $claim->id_report)
            ->where('id', '!=', $claim->id)
            ->where('status_klaim', 'pending')
            ->update(['status_klaim' => 'rejected']);

        // Notifikasi ke user yang klaim (database + WA via ClaimStatusNotification->via())
        $claim->user->notify(new ClaimStatusNotification($claim, $claim->report, 'approved'));
        ClaimStatusEvent::dispatch($claim, $claim->report, 'approved');

        return redirect()->back()
            ->with('success', 'Klaim disetujui. Laporan otomatis ditandai selesai.');
    }

    // Reject klaim
    public function reject(Request $request, $id)
    {
        $claim = Claim::with('user', 'report')->findOrFail($id);

        $claim->update([
            'status_klaim' => 'rejected',
        ]);

        // Notifikasi ke user yang klaim
        $claim->user->notify(new ClaimStatusNotification(
            $claim,
            $claim->report,
            'rejected',
            $request->input('admin_note')
        ));
        ClaimStatusEvent::dispatch($claim, $claim->report, 'rejected');

        return redirect()->back()
            ->with('success', 'Klaim berhasil di-reject.');
    }
}