<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function categories()
    {
        return response()->json(Category::all());
    }

    public function campuses()
    {
        return response()->json(
            Campus::where('is_active', true)->orderBy('nama_kampus')->get()
        );
    }

    public function index(Request $request)
    {
        $query = Report::with(['category', 'user', 'campus'])
            ->where('status', 'approved');

        if ($request->filled('jenis')) {
            $query->where('jenis_laporan', $request->jenis);
        }

        if ($request->filled('campus_id')) {
            $query->where('id_campus', $request->campus_id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', $search)
                  ->orWhere('deskripsi', 'like', $search)
                  ->orWhere('lokasi', 'like', $search);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('id_category', $request->category_id);
        }

        $reports = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $reports,
        ]);
    }

    public function myReports(Request $request)
    {
        $reports = Report::with(['category', 'campus'])
            ->where('id_user', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $reports,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_category'      => 'required|exists:categories,id',
            'id_campus'        => 'nullable|exists:campuses,id',
            'jenis_laporan'    => 'required|in:hilang,temuan',
            'nama_barang'      => 'required|string|max:255',
            'deskripsi'        => 'required|string|min:10',
            'lokasi'           => 'required|string|max:255',
            'tanggal_kejadian' => 'required|date',
            'foto_barang'      => 'nullable|image|max:3072',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_barang')) {
            $fotoPath = $this->uploadPhoto($request->file('foto_barang'));
        }

        $campusId = $validated['id_campus'] ?? $request->user()->id_campus;

        $report = Report::create([
            'id_user'          => $request->user()->id,
            'id_category'      => $validated['id_category'],
            'id_campus'        => $campusId,
            'jenis_laporan'    => $validated['jenis_laporan'],
            'nama_barang'      => $validated['nama_barang'],
            'deskripsi'        => $validated['deskripsi'],
            'lokasi'           => $validated['lokasi'],
            'tanggal_kejadian' => $validated['tanggal_kejadian'],
            'foto_barang'      => $fotoPath,
            'status'           => 'pending',
        ]);

        $report->load(['category', 'campus']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat dan sedang menunggu verifikasi admin.',
            'data'    => $report,
        ], 201);
    }

    private function uploadPhoto($file): string
    {
        $supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $anonKey = config('services.supabase.anon_key');

        if ($supabaseUrl && $anonKey) {
            try {
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $fileName = round(microtime(true) * 1000) . '_' . (auth()->id() ?? 'api') . '.' . $ext;
                $objectPath = 'reports/' . $fileName;

                $res = \Illuminate\Support\Facades\Http::withHeaders([
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
                \Illuminate\Support\Facades\Log::warning('Supabase storage upload fallback: ' . $e->getMessage());
            }
        }

        return $file->store('foto_barang', 'public');
    }
}
