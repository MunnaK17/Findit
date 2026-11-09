<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function myClaims(Request $request)
    {
        $claims = Claim::with(['report.category'])
            ->where('id_user', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $claims,
        ]);
    }

    public function store(Request $request, $reportId)
    {
        $report = Report::where('jenis_laporan', 'temuan')
            ->where('status', 'approved')
            ->findOrFail($reportId);

        if ($report->id_user === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa mengklaim laporan milik Anda sendiri.',
            ], 422);
        }

        $alreadyClaimed = Claim::where('id_report', $reportId)
            ->where('id_user', $request->user()->id)
            ->where('status_klaim', '!=', 'rejected')
            ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah mengajukan klaim untuk barang ini.',
            ], 422);
        }

        $request->validate([
            'pesan_klaim' => 'required|string|min:15|max:1000',
        ]);

        $claim = Claim::create([
            'id_report'     => $reportId,
            'id_user'       => $request->user()->id,
            'pesan_klaim'   => $request->pesan_klaim,
            'status_klaim'  => 'pending',
            'tanggal_klaim' => now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Klaim berhasil diajukan dan sedang diverifikasi admin.',
            'data'    => $claim,
        ], 201);
    }

    public function cancel(Request $request, $id)
    {
        $claim = Claim::where('id_user', $request->user()->id)->findOrFail($id);

        if ($claim->status_klaim !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya klaim berstatus pending yang dapat dibatalkan.',
            ], 422);
        }

        $claim->delete();

        return response()->json([
            'success' => true,
            'message' => 'Klaim berhasil dibatalkan.',
        ]);
    }
}
