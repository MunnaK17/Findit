<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\Request;

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
        $claim = Claim::with('report')->findOrFail($id);

        $claim->update(['status_klaim' => 'approved']);

        // Otomatis update status laporan jadi completed
        $claim->report->update(['status' => 'completed']);

        // Reject semua klaim lain untuk laporan yang sama
        Claim::where('id_report', $claim->id_report)
            ->where('id', '!=', $claim->id)
            ->where('status_klaim', 'pending')
            ->update(['status_klaim' => 'rejected']);

        return redirect()->back()
            ->with('success', 'Klaim disetujui. Laporan otomatis ditandai selesai.');
    }

    // Reject klaim
    public function reject($id)
    {
        $claim = Claim::findOrFail($id);
        $claim->update(['status_klaim' => 'rejected']);

        return redirect()->back()
            ->with('success', 'Klaim berhasil di-reject.');
    }
}