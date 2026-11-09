<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Report;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Moderasi laporan. Email & notifikasi in-app ke pelapor dikirim otomatis oleh trigger
 * Supabase (notify_email_dispatch) setiap status berubah, jadi controller ini tidak mengirim apa pun.
 */
class ReportController extends Controller
{
    // Daftar semua laporan dengan filter
    public function index(Request $request)
    {
        $query = Report::with(['user', 'category', 'campus']);

        if ($request->jenis) {
            $query->where('jenis_laporan', $request->jenis);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->filled('campus')) {
            $query->where('id_campus', $request->campus);
        }

        if ($request->search) {
            $search = '%' . addcslashes($request->search, '%_\\') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ilike', $search)
                  ->orWhere('deskripsi', 'ilike', $search)
                  ->orWhere('lokasi', 'ilike', $search);
            });
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $reports    = $query->paginate(15)->withQueryString();
        $categories = Category::all();
        $campuses   = Campus::orderBy('nama_kampus')->get();

        return view('admin.reports.index', compact('reports', 'categories', 'campuses'));
    }

    // Detail laporan
    public function show($id)
    {
        $report = Report::with(['user', 'category', 'campus', 'claims.user'])->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    // Approve laporan (baru atau setelah diedit pelapor)
    public function approve($id)
    {
        $report = Report::findOrFail($id);

        if (!in_array($report->status, ['pending', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Hanya laporan yang menunggu verifikasi atau ditolak yang bisa disetujui.');
        }

        // Masa tayang dihitung dari saat disetujui agar laporan tidak langsung kedaluwarsa.
        $tayangSampai = now()->addDays(Report::MASA_TAYANG_HARI);
        $report->update([
            'status'     => 'approved',
            'expires_at' => $report->expires_at && $report->expires_at->gt($tayangSampai)
                ? $report->expires_at
                : $tayangSampai,
        ]);

        return redirect()->back()
            ->with('success', 'Laporan disetujui dan tayang sampai ' . $report->expires_at->translatedFormat('d M Y') . '.');
    }

    // Reject laporan
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'min:10'],
        ]);

        $report = Report::findOrFail($id);

        if (!in_array($report->status, ['pending', 'approved'], true)) {
            return redirect()->back()->with('error', 'Laporan yang sudah selesai atau ditolak tidak bisa ditolak lagi.');
        }

        $report->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->back()
            ->with('success', 'Laporan berhasil di-reject.');
    }

    // Complete laporan
    public function complete($id)
    {
        $report = Report::findOrFail($id);

        if ($report->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya laporan yang sudah disetujui yang bisa ditandai selesai.');
        }

        $report->update(['status' => 'completed']);

        return redirect()->back()
            ->with('success', 'Laporan ditandai selesai.');
    }

    // Hapus laporan
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->foto_barang && !str_starts_with($report->foto_barang, 'http')) {
            Storage::disk('public')->delete($report->foto_barang);
        } else {
            // Butuh SUPABASE_SERVICE_ROLE_KEY: file milik pelapor, bukan milik admin.
            app(SupabaseStorage::class)->deleteByUrl($report->foto_barang);
        }

        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
