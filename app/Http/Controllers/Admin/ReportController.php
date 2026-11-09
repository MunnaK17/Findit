<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Category;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Daftar semua laporan dengan filter
    public function index(Request $request)
    {
        $query = Report::with(['user', 'category']);

        if ($request->jenis) {
            $query->where('jenis_laporan', $request->jenis);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = '%' . $request->search . '%';
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

        $reports    = $query->paginate(15);
        $categories = Category::all();

        return view('admin.reports.index', compact('reports', 'categories'));
    }

    // Detail laporan
    public function show($id)
    {
        $report = Report::with(['user', 'category', 'claims.user'])->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    // Approve laporan
    public function approve($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'approved']);

        return redirect()->back()
            ->with('success', 'Laporan berhasil di-approve.');
    }

    // Reject laporan
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'min:10'],
        ]);

        $report = Report::findOrFail($id);
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
        $report->update(['status' => 'completed']);

        return redirect()->back()
            ->with('success', 'Laporan ditandai selesai.');
    }

    // Hapus laporan
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->foto_barang && !str_starts_with($report->foto_barang, 'http')) {
            \Storage::disk('public')->delete($report->foto_barang);
        }

        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}