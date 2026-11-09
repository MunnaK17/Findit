<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Category;
use App\Models\Claim;
use App\Models\Report;
use App\Notifications\ReportSubmittedNotification;
use App\Services\MathCaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    // Halaman Cari Barang terpadu (Semua / Hilang / Temuan + Filter Cabang & Kategori)
    public function cari(Request $request)
    {
        $query = Report::with(['category', 'user', 'campus'])
            ->whereIn('status', ['approved', 'completed']);

        if (in_array($request->jenis, ['hilang', 'temuan'], true)) {
            $query->where('jenis_laporan', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ilike', $search)
                  ->orWhere('deskripsi', 'ilike', $search)
                  ->orWhere('lokasi', 'ilike', $search);
            });
        }

        if ($request->filled('category')) {
            $query->where('id_category', $request->category);
        }

        if ($request->filled('campus')) {
            $query->where('id_campus', $request->campus);
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $reports = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('id')->get();
        $campuses = Campus::where('is_active', true)->orderBy('nama_kampus')->get();

        return view('reports.search', compact('reports', 'categories', 'campuses'));
    }

    // Daftar barang hilang (public)
    public function hilang(Request $request)
    {
        $query = Report::with(['category', 'user', 'campus'])
            ->where('jenis_laporan', 'hilang')
            ->where('status', 'approved');

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ilike', $search)
                  ->orWhere('deskripsi', 'ilike', $search)
                  ->orWhere('lokasi', 'ilike', $search);
            });
        }

        if ($request->category) {
            $query->where('id_category', $request->category);
        }

        if ($request->campus) {
            $query->where('id_campus', $request->campus);
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $reports = $query->paginate(9)->withQueryString();
        $categories = Category::all();
        $campuses = Campus::where('is_active', true)->orderBy('nama_kampus')->get();

        return view('reports.hilang', compact('reports', 'categories', 'campuses'));
    }

    // Daftar barang temuan (public)
    public function temuan(Request $request)
    {
        $query = Report::with(['category', 'user', 'campus'])
            ->where('jenis_laporan', 'temuan')
            ->where('status', 'approved');

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'ilike', $search)
                  ->orWhere('deskripsi', 'ilike', $search)
                  ->orWhere('lokasi', 'ilike', $search);
            });
        }

        if ($request->category) {
            $query->where('id_category', $request->category);
        }

        if ($request->campus) {
            $query->where('id_campus', $request->campus);
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $reports = $query->paginate(9)->withQueryString();
        $categories = Category::all();
        $campuses = Campus::where('is_active', true)->orderBy('nama_kampus')->get();

        return view('reports.temuan', compact('reports', 'categories', 'campuses'));
    }

    // Detail laporan (public)
    public function show($id)
    {
        $report = Report::with(['category', 'user', 'campus', 'claims'])->findOrFail($id);
        return view('reports.show', compact('report'));
    }

    // Form buat laporan (auth)
    public function create()
    {
        $captchaService = app(MathCaptchaService::class);
        $captcha = $captchaService->generate();

        $categories = Category::all();
        $campuses = Campus::where('is_active', true)->orderBy('nama_kampus')->get();

        return view('reports.create', [
            'categories' => $categories,
            'campuses' => $campuses,
            'captcha_question' => $captcha['question'],
        ]);
    }

    // Simpan laporan (auth)
    public function store(Request $request)
    {
        $captchaService = app(MathCaptchaService::class);

        $request->validate([
            'id_category'      => ['required', 'exists:categories,id'],
            'id_campus'        => ['required', 'exists:campuses,id'],
            'jenis_laporan'    => ['required', 'in:hilang,temuan'],
            'nama_barang'      => ['required', 'string', 'max:255'],
            'deskripsi'        => ['required', 'string', 'min:10'],
            'lokasi'           => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'foto_barang'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'captcha_answer'   => ['required', 'integer', 'min:0', 'max:18'],
        ]);

        if (!$captchaService->validate((int) $request->captcha_answer)) {
            return back()
                ->withInput($request->except('captcha_answer'))
                ->withErrors(['captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.'])
                ->with(['captcha_question' => $captchaService->generate()['question']]);
        }

        $fotoPath = null;
        if ($request->hasFile('foto_barang')) {
            $fotoPath = $this->uploadPhoto($request->file('foto_barang'));
        }

        $report = Report::create([
            'id_user'          => Auth::id(),
            'id_category'      => $request->id_category,
            'id_campus'        => $request->id_campus,
            'jenis_laporan'    => $request->jenis_laporan,
            'nama_barang'      => $request->nama_barang,
            'deskripsi'        => $request->deskripsi,
            'lokasi'           => $request->lokasi,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'foto_barang'      => $fotoPath,
            'status'           => 'pending',
        ]);

        try {
            if ($report->user) {
                $report->user->notify(new ReportSubmittedNotification($report, $request->jenis_laporan));
            }
        } catch (\Throwable $e) {
            Log::error('Gagal kirim ReportSubmittedNotification: ' . $e->getMessage());
        }

        return redirect()->route('activity.index')
            ->with('success', 'Laporan berhasil diajukan dan sedang menunggu verifikasi admin sebelum dipublikasikan ke beranda.');
    }

    // Halaman Aktivitas terpadu: Laporan Saya & Klaim Saya (seperti di Mobile)
    public function aktivitas(Request $request)
    {
        $tab = $request->get('tab', 'laporan');

        $reportsQuery = Report::with(['category', 'campus'])
            ->where('id_user', Auth::id());

        if ($request->status) {
            $reportsQuery->where('status', $request->status);
        }
        if ($request->jenis) {
            $reportsQuery->where('jenis_laporan', $request->jenis);
        }

        $reports = $reportsQuery->latest()->paginate(10, ['*'], 'reports_page')->withQueryString();

        $claims = Claim::with(['report.category', 'report.campus'])
            ->where('id_user', Auth::id())
            ->latest()
            ->paginate(10, ['*'], 'claims_page')
            ->withQueryString();

        return view('activity.index', compact('reports', 'claims', 'tab'));
    }

    // Laporan milik user yang login
    public function myReports(Request $request)
    {
        $query = Report::with(['category', 'campus'])
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

        if (!in_array($report->status, ['pending', 'approved'], true)) {
            return redirect()->route('my.reports')
                ->with('error', 'Laporan yang sudah selesai tidak dapat diedit.');
        }

        $categories = Category::all();
        $campuses = Campus::where('is_active', true)->orderBy('nama_kampus')->get();

        return view('reports.edit', compact('report', 'categories', 'campuses'));
    }

    // Update laporan
    public function update(Request $request, $id)
    {
        $report = Report::where('id_user', Auth::id())->findOrFail($id);

        if (!in_array($report->status, ['pending', 'approved'], true)) {
            return redirect()->route('my.reports')
                ->with('error', 'Laporan yang sudah selesai tidak dapat diedit.');
        }

        $request->validate([
            'id_category'      => ['required', 'exists:categories,id'],
            'id_campus'        => ['required', 'exists:campuses,id'],
            'nama_barang'      => ['required', 'string', 'max:255'],
            'deskripsi'        => ['required', 'string', 'min:10'],
            'lokasi'           => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'foto_barang'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $fotoPath = $report->foto_barang;
        if ($request->hasFile('foto_barang')) {
            $fotoPath = $this->uploadPhoto($request->file('foto_barang'));
        }

        $report->update([
            'id_category'      => $request->id_category,
            'id_campus'        => $request->id_campus,
            'nama_barang'      => $request->nama_barang,
            'deskripsi'        => $request->deskripsi,
            'lokasi'           => $request->lokasi,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'foto_barang'      => $fotoPath,
        ]);

        return redirect()->route('activity.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    // Hapus laporan
    public function destroy($id)
    {
        $report = Report::where('id_user', Auth::id())->findOrFail($id);

        if ($report->foto_barang && !str_starts_with($report->foto_barang, 'http')) {
            Storage::disk('public')->delete($report->foto_barang);
        }

        $report->delete();

        return redirect()->route('activity.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Upload foto ke Supabase Storage bucket `reports` agar sinkron dengan aplikasi mobile,
     * dengan fallback ke storage lokal.
     */
    private function uploadPhoto($file): string
    {
        $supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $anonKey = config('services.supabase.anon_key');

        if ($supabaseUrl && $anonKey) {
            try {
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $fileName = round(microtime(true) * 1000) . '_' . (Auth::id() ?? 'web') . '.' . $ext;
                $objectPath = 'reports/' . $fileName;

                $res = Http::withHeaders([
                    'apikey' => $anonKey,
                    'Authorization' => 'Bearer ' . $anonKey,
                    'x-upsert' => 'true',
                ])->withBody(
                    file_get_contents($file->getRealPath()),
                    $file->getMimeType() ?: 'image/jpeg'
                )->post("{$supabaseUrl}/storage/v1/object/reports/{$objectPath}");

                if ($res->successful()) {
                    return "{$supabaseUrl}/storage/v1/object/public/reports/{$objectPath}";
                }
            } catch (\Throwable $e) {
                Log::warning('Supabase storage upload fallback to local: ' . $e->getMessage());
            }
        }

        return $file->store('foto_barang', 'public');
    }
}
