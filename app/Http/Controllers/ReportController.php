<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    // Daftar barang hilang (public)
    public function hilang(Request $request)
    {
        $query = Report::with(['category', 'user'])
            ->where('jenis_laporan', 'hilang')
            ->where('status', 'approved');

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', $search)
                  ->orWhere('deskripsi', 'like', $search)
                  ->orWhere('lokasi', 'like', $search);
            });
        }

        if ($request->category) {
            $query->where('id_category', $request->category);
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $reports = $query->paginate(9);
        $categories = Category::all();

        return view('reports.hilang', compact('reports', 'categories'));
    }

    // Daftar barang temuan (public)
    public function temuan(Request $request)
    {
        $query = Report::with(['category', 'user'])
            ->where('jenis_laporan', 'temuan')
            ->where('status', 'approved');

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', $search)
                  ->orWhere('deskripsi', 'like', $search)
                  ->orWhere('lokasi', 'like', $search);
            });
        }

        if ($request->category) {
            $query->where('id_category', $request->category);
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $reports = $query->paginate(9);
        $categories = Category::all();

        return view('reports.temuan', compact('reports', 'categories'));
    }

    // Detail laporan (public)
    public function show($id)
    {
        $report = Report::with(['category', 'user', 'claims'])->findOrFail($id);
        return view('reports.show', compact('report'));
    }

    // Form buat laporan (auth)
    public function create()
    {
        $categories = Category::all();
        return view('reports.create', compact('categories'));
    }

    // Simpan laporan (auth)
    public function store(Request $request)
    {
        $request->validate([
            'id_category'      => ['required', 'exists:categories,id'],
            'jenis_laporan'    => ['required', 'in:hilang,temuan'],
            'nama_barang'      => ['required', 'string', 'max:255'],
            'deskripsi'        => ['required', 'string', 'min:20'],
            'lokasi'           => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'foto_barang'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'min:10kb', 'max:2048', 'dimensions:min_width=100,min_height=100'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_barang')) {
            $fotoPath = $request->file('foto_barang')->store('foto_barang', 'public');
        }

        Report::create([
            'id_user'          => Auth::id(),
            'id_category'      => $request->id_category,
            'jenis_laporan'    => $request->jenis_laporan,
            'nama_barang'      => $request->nama_barang,
            'deskripsi'        => $request->deskripsi,
            'lokasi'           => $request->lokasi,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'foto_barang'      => $fotoPath,
            'status'           => 'pending',
        ]);

        return redirect()->route('my.reports')
            ->with('success', 'Laporan berhasil dikirim! Menunggu verifikasi admin.');
    }

    // Laporan milik user yang login
    public function myReports(Request $request)
    {
    $query = Report::with('category')
        ->where('id_user', Auth::id());

    if ($request->status) {
        $query->where('status', $request->status);
    }

    if ($request->jenis) {
        $query->where('jenis_laporan', $request->jenis);
    }

    $reports = $query->latest()->paginate(10);

    return view('reports.my-reports', compact('reports'));
    }

    // Form edit laporan
    public function edit($id)
    {
        $report = Report::where('id_user', Auth::id())->findOrFail($id);

        // Hanya bisa edit jika masih pending
        if ($report->status !== 'pending') {
            return redirect()->route('my.reports')
                ->with('error', 'Laporan yang sudah diproses tidak dapat diedit.');
        }

        $categories = Category::all();
        return view('reports.edit', compact('report', 'categories'));
    }

    // Update laporan
    public function update(Request $request, $id)
    {
        $report = Report::where('id_user', Auth::id())->findOrFail($id);

        if ($report->status !== 'pending') {
            return redirect()->route('my.reports')
                ->with('error', 'Laporan yang sudah diproses tidak dapat diedit.');
        }

        $request->validate([
            'id_category'      => ['required', 'exists:categories,id'],
            'nama_barang'      => ['required', 'string', 'max:255'],
            'deskripsi'        => ['required', 'string', 'min:20'],
            'lokasi'           => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'foto_barang'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'min:10kb', 'max:2048', 'dimensions:min_width=100,min_height=100'],
        ]);

        $fotoPath = $report->foto_barang;
        if ($request->hasFile('foto_barang')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_barang')->store('foto_barang', 'public');
        }

        $report->update([
            'id_category'      => $request->id_category,
            'nama_barang'      => $request->nama_barang,
            'deskripsi'        => $request->deskripsi,
            'lokasi'           => $request->lokasi,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'foto_barang'      => $fotoPath,
        ]);

        return redirect()->route('my.reports')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    // Hapus laporan
    public function destroy($id)
    {
        $report = Report::where('id_user', Auth::id())->findOrFail($id);

        if ($report->status !== 'pending') {
            return redirect()->route('my.reports')
                ->with('error', 'Laporan yang sudah diproses tidak dapat dihapus.');
        }

        if ($report->foto_barang) {
            Storage::disk('public')->delete($report->foto_barang);
        }

        $report->delete();

        return redirect()->route('my.reports')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}