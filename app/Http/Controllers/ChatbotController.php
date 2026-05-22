<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $userMessage = strtolower(trim($request->message));

        // Statistik
        $totalTemuan = Report::where('jenis_laporan', 'temuan')->where('status', 'approved')->count();
        $totalHilang = Report::where('jenis_laporan', 'hilang')->where('status', 'approved')->count();
        $totalSelesai = Report::where('status', 'completed')->count();
        $categories = Category::pluck('nama_category')->join(', ');

        // Barang temuan terbaru
        $temuanTerbaru = Report::with('category')
            ->where('jenis_laporan', 'temuan')
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($r) {
                $namaKategori = $r->category->nama_category ?? 'Tanpa kategori';
                return "- {$r->nama_barang} ({$namaKategori}) di {$r->lokasi}";
            })
            ->join("\n");

        // Barang hilang terbaru
        $hilangTerbaru = Report::with('category')
            ->where('jenis_laporan', 'hilang')
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($r) {
                $namaKategori = $r->category->nama_category ?? 'Tanpa kategori';
                return "- {$r->nama_barang} ({$namaKategori}) di {$r->lokasi}";
            })
            ->join("\n");

        $systemPrompt = <<<EOT
Kamu adalah asisten virtual FindIt, sistem Lost & Found kampus BSI.
Tugasmu membantu mahasiswa menemukan informasi barang hilang dan temuan di kampus.

DATA TERKINI:
- Barang temuan aktif: {$totalTemuan} item
- Barang hilang aktif: {$totalHilang} item
- Berhasil dikembalikan: {$totalSelesai} item
- Kategori tersedia: {$categories}

BARANG TEMUAN TERBARU:
{$temuanTerbaru}

BARANG HILANG TERBARU:
{$hilangTerbaru}

PANDUAN MENJAWAB:
- Jawab dalam Bahasa Indonesia yang ramah dan singkat
- Maksimal 3-4 kalimat per jawaban
- Jika ditanya barang spesifik, arahkan ke halaman pencarian
- Jika tidak ada info, sarankan buat laporan atau hubungi admin
- Jangan membuat informasi yang tidak ada di data di atas
- Jika pertanyaan di luar konteks FindIt, arahkan kembali ke fitur website FindIt
EOT;

        try {
            $apiKey = config('services.gemini.api_key');
            $model = config('services.gemini.model', 'gemini-2.0-flash');

            if (!empty($apiKey)) {
                $prompt = $systemPrompt . "\n\nPertanyaan pengguna: " . $request->message;

                $response = Http::timeout(20)
                    ->acceptJson()
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => $prompt]
                                    ]
                                ]
                            ],
                            'generationConfig' => [
                                'temperature' => 0.7,
                                'maxOutputTokens' => 300,
                            ],
                        ]
                    );

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return response()->json([
                            'reply' => trim($data['candidates'][0]['content']['parts'][0]['text']),
                            'action' => $this->detectAction($userMessage),
                        ]);
                    }
                }
            }

            // FALLBACK LOCAL RESPONSE
            $fallback = $this->getFallbackReply(
                $userMessage,
                $totalTemuan,
                $totalHilang,
                $totalSelesai,
                $temuanTerbaru,
                $hilangTerbaru,
                $categories
            );

            return response()->json([
                'reply' => $fallback['reply'],
                'action' => $fallback['action'],
            ]);

        } catch (\Exception $e) {
            $fallback = $this->getFallbackReply(
                $userMessage,
                $totalTemuan,
                $totalHilang,
                $totalSelesai,
                $temuanTerbaru,
                $hilangTerbaru,
                $categories
            );

            return response()->json([
                'reply' => $fallback['reply'],
                'action' => $fallback['action'],
            ]);
        }
    }

    private function detectAction($message)
    {
        if (
            str_contains($message, 'buat laporan') ||
            str_contains($message, 'lapor barang') ||
            str_contains($message, 'melaporkan barang')
        ) {
            return [
                'label' => 'Buat Laporan',
                'url' => route('reports.create'),
            ];
        }

        if (
            str_contains($message, 'barang hilang') &&
            (str_contains($message, 'lihat') || str_contains($message, 'cek') || str_contains($message, 'daftar'))
        ) {
            return [
                'label' => 'Lihat Barang Hilang',
                'url' => route('reports.hilang'),
            ];
        }

        if (
            str_contains($message, 'barang temuan') &&
            (str_contains($message, 'lihat') || str_contains($message, 'cek') || str_contains($message, 'daftar'))
        ) {
            return [
                'label' => 'Lihat Barang Temuan',
                'url' => route('reports.temuan'),
            ];
        }

        if (
            str_contains($message, 'laporan saya') ||
            str_contains($message, 'cek laporan saya')
        ) {
            return [
                'label' => 'Laporan Saya',
                'url' => route('my.reports'),
            ];
        }

        if (
            str_contains($message, 'klaim saya') ||
            str_contains($message, 'cek klaim saya')
        ) {
            return [
                'label' => 'Klaim Saya',
                'url' => route('my.claims'),
            ];
        }

        return null;
    }

    private function getFallbackReply($message, $totalTemuan, $totalHilang, $totalSelesai, $temuanTerbaru, $hilangTerbaru, $categories)
    {
        // 1. INFO BARANG HILANG TERBARU
        if (
            (str_contains($message, 'barang hilang') && (str_contains($message, 'terbaru') || str_contains($message, 'info') || str_contains($message, 'daftar')))
            || str_contains($message, 'hilang terbaru')
            || str_contains($message, 'laporan barang hilang')
        ) {
            return [
                'reply' => $hilangTerbaru
                    ? "Berikut beberapa laporan barang hilang terbaru:\n" . $hilangTerbaru
                    : 'Saat ini belum ada laporan barang hilang terbaru.',
                'action' => [
                    'label' => 'Lihat Barang Hilang',
                    'url' => route('reports.hilang'),
                ],
            ];
        }

        // 2. INFO BARANG TEMUAN TERBARU
        if (
            (str_contains($message, 'barang temuan') && (str_contains($message, 'terbaru') || str_contains($message, 'info') || str_contains($message, 'daftar')))
            || str_contains($message, 'temuan terbaru')
            || str_contains($message, 'laporan barang temuan')
        ) {
            return [
                'reply' => $temuanTerbaru
                    ? "Berikut beberapa barang temuan terbaru:\n" . $temuanTerbaru
                    : 'Saat ini belum ada data barang temuan terbaru.',
                'action' => [
                    'label' => 'Lihat Barang Temuan',
                    'url' => route('reports.temuan'),
                ],
            ];
        }

        // 3. CARA LAPOR BARANG HILANG
        if (
            str_contains($message, 'cara lapor') ||
            str_contains($message, 'lapor barang hilang') ||
            str_contains($message, 'melaporkan barang hilang')
        ) {
            return [
                'reply' => 'Untuk melaporkan barang hilang, silakan login lalu buka menu "Buat Laporan". Isi nama barang, kategori, lokasi, tanggal, dan deskripsi barang.',
                'action' => [
                    'label' => 'Buat Laporan',
                    'url' => route('reports.create'),
                ],
            ];
        }

        // 4. CARA KLAIM BARANG TEMUAN
        if (
            str_contains($message, 'cara klaim') ||
            str_contains($message, 'klaim barang') ||
            str_contains($message, 'ajukan klaim')
        ) {
            return [
                'reply' => 'Untuk klaim barang temuan, buka detail barang temuan lalu klik tombol "Ajukan Klaim". Pastikan data yang kamu isi sesuai agar admin bisa memverifikasi.',
                'action' => [
                    'label' => 'Lihat Barang Temuan',
                    'url' => route('reports.temuan'),
                ],
            ];
        }

        // 5. STATISTIK
        if (
            str_contains($message, 'statistik') ||
            str_contains($message, 'jumlah') ||
            str_contains($message, 'berapa barang')
        ) {
            return [
                'reply' => "Saat ini terdapat {$totalTemuan} barang temuan aktif, {$totalHilang} barang hilang aktif, dan {$totalSelesai} barang berhasil dikembalikan.",
                'action' => null,
            ];
        }

        // 6. KATEGORI
        if (str_contains($message, 'kategori')) {
            return [
                'reply' => "Kategori yang tersedia saat ini: {$categories}.",
                'action' => null,
            ];
        }

        return [
            'reply' => 'Saya siap membantu seputar FindIt 😊 Kamu bisa tanya tentang info barang hilang terbaru, barang temuan terbaru, cara melaporkan barang hilang, cara klaim barang temuan, statistik, kategori, atau cek laporan saya.',
            'action' => null,
        ];
    }
}