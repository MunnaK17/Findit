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

        // Cek apakah ada barang
        $hasTemuan = Report::where('jenis_laporan', 'temuan')->where('status', 'approved')->exists();
        $hasHilang = Report::where('jenis_laporan', 'hilang')->where('status', 'approved')->exists();

        // Daftar temuan dengan detail lengkap
        $temuanList = Report::with('category')
            ->where('jenis_laporan', 'temuan')
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        $hilangList = Report::with('category')
            ->where('jenis_laporan', 'hilang')
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        $systemPrompt = <<<EOT
Kamu adalah asisten virtual FindIt, sistem Lost& Found kampus BSI.
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

            // FALLBACK LOCAL RESPONSE - LEBIH CERDAS
            $reply = $this->generateSmartReply(
                $userMessage,
                $totalTemuan,
                $totalHilang,
                $totalSelesai,
                $temuanTerbaru,
                $hilangTerbaru,
                $categories,
                $hasTemuan,
                $hasHilang,
                $temuanList,
                $hilangList
            );

            return response()->json([
                'reply' => $reply['reply'],
                'action' => $reply['action'],
                'quickReplies' => $reply['quickReplies'] ?? [],
            ]);

        } catch (\Exception $e) {
            $reply = $this->generateSmartReply(
                $userMessage,
                $totalTemuan,
                $totalHilang,
                $totalSelesai,
                $temuanTerbaru,
                $hilangTerbaru,
                $categories,
                $hasTemuan,
                $hasHilang,
                $temuanList,
                $hilangList
            );

            return response()->json([
                'reply' => $reply['reply'],
                'action' => $reply['action'],
                'quickReplies' => $reply['quickReplies'] ?? [],
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

    private function generateSmartReply($message, $totalTemuan, $totalHilang, $totalSelesai, $temuanTerbaru, $hilangTerbaru, $categories, $hasTemuan, $hasHilang, $temuanList, $hilangList)
    {
        // === DETEKSI TOPIK UTAMA ===

        $isTemuan = $this->containsAny($message, ['temuan', 'menemukan', 'ditemukan', 'yang ditemukan', 'barang yang ditemukan']);
        $isHilang = $this->containsAny($message, ['hilang', 'kehilangan', 'yang hilang', 'dicari', 'mencari', 'barang yang saya cari']);
        $isStatistik = $this->containsAny($message, ['statistik', 'jumlah', 'berapa', 'total', 'ada berapa', 'info']);
        $isKategori = $this->containsAny($message, ['kategori', 'jenis', 'tipe', 'macam']);
        $isCaraLapor = $this->containsAny($message, ['cara', 'lapor', 'laporkan', 'report', 'buat laporan', 'bagaimana']);
        $isCaraKlaim = $this->containsAny($message, ['klaim', 'claim', 'ambil', 'dapatkan', 'menambahkan']);
        $isDaftar = $this->containsAny($message, ['daftar', 'list', 'listrik', 'tampilkan']);
        $isSedia = $this->containsAny($message, ['tersedia', 'sedia', 'ada yang', 'apa saja']);
        $isGreeting = $this->containsAny($message, ['halo', 'hi', 'hai', 'hey', 'assalam', 'pagi', 'siang', 'sore', 'malam', 'selamat']);
        $isTerimaKasih = $this->containsAny($message, ['terima kasih', 'thanks', 'thx', 'makasih', 'okeh', 'oke', 'ok']);
        $isCari = $this->containsAny($message, ['cari', 'search', 'ketemu', ' cari ']);
        $isBantu = $this->containsAny($message, ['bantu', 'tolong', 'bantuan', 'help']);

        // === HELPER: Generate Quick Replies ===
        $quickReplies = [
            ['label' => '📦 Barang Temuan', 'value' => 'barang temuan terbaru'],
            ['label' => '🔍 Barang Hilang', 'value' => 'barang hilang terbaru'],
            ['label' => '📊 Statistik', 'value' => 'statistik findit'],
            ['label' => '📁 Kategori', 'value' => 'kategori barang'],
        ];

        // === LOGIKA PRIORITAS ===

        // 1. SALAM SAPAAN
        if ($isGreeting) {
            $msg = "Halo! 👋 Selamat datang di FindIt!\n\n";
            $msg .= "Saya siap membantu kamu menemukan info barang hilang & temuan di kampus.\n\n";
            $msg .= "Silakan pilih atau ketik pertanyaanmu!";
            return [
                'reply' => $msg,
                'action' => null,
                'quickReplies' => $quickReplies,
            ];
        }

        // 2. TERIMA KASIH / OKE
        if ($isTerimaKasih) {
            return [
                'reply' => "Sama-sama! 😊 Semoga barangmu segera ketemu. Kalau butuh bantuan lagi, jangan ragu untuk bertanya ya!",
                'action' => null,
                'quickReplies' => $quickReplies,
            ];
        }

        // 3. TANYA TOLONG / BANTUAN
        if ($isBantu && !$isGreeting) {
            $msg = "Tentu, saya siap membantu! 🙌\n\n";
            $msg .= "Saya bisa bantu kamu untuk:\n";
            $msg .= "• 🔍 Mencari info barang hilang/temuan\n";
            $msg .= "• 📊 Melihat statistik FindIt\n";
            $msg .= "• 📁 Mengetahui kategori barang\n";
            $msg .= "• 📝 Cara membuat laporan\n";
            $msg .= "• 🎁 Cara klaim barang temuan\n\n";
            $msg .= "Silakan pilih atau ketik pertanyaanmu!";
            return [
                'reply' => $msg,
                'action' => null,
                'quickReplies' => $quickReplies,
            ];
        }

        // 4. BARANG TEMUAN (prioritas tinggi jika ada kata terkait temuan)
        if ($isTemuan) {
            if ($hasTemuan) {
                $reply = "📦 Saat ini ada {$totalTemuan} barang temuan di kampus!\n\n";
                $reply .= "Berikut barang temuan terbaru:\n";
                $reply .= $temuanTerbaru ?: "Data sedang dimuat...";
                return [
                    'reply' => $reply,
                    'action' => ['label' => 'Lihat Semua Barang Temuan', 'url' => route('reports.temuan')],
                    'quickReplies' => [
                        ['label' => '🔍 Barang Hilang', 'value' => 'barang hilang terbaru'],
                        ['label' => '📝 Buat Laporan', 'value' => 'cara buat laporan'],
                        ['label' => '📊 Statistik', 'value' => 'statistik findit'],
                    ],
                ];
            } else {
                return [
                    'reply' => '😔 Belum ada barang temuan yang dilaporkan saat ini.\n\nTapi kalau kamu menemukan barang, segera laporkan ya! 😊',
                    'action' => ['label' => 'Lihat Semua', 'url' => route('reports.temuan')],
                    'quickReplies' => $quickReplies,
                ];
            }
        }

        // 5. BARANG HILANG (prioritas tinggi jika ada kata terkait hilang)
        if ($isHilang) {
            if ($hasHilang) {
                $reply = "🔍 Saat ini ada {$totalHilang} laporan barang hilang di kampus.\n\n";
                $reply .= "Berikut barang hilang terbaru:\n";
                $reply .= $hilangTerbaru ?: "Data sedang dimuat...";
                return [
                    'reply' => $reply,
                    'action' => ['label' => 'Lihat Semua Barang Hilang', 'url' => route('reports.hilang')],
                    'quickReplies' => [
                        ['label' => '📦 Barang Temuan', 'value' => 'barang temuan terbaru'],
                        ['label' => '📝 Buat Laporan', 'value' => 'cara buat laporan'],
                        ['label' => '📊 Statistik', 'value' => 'statistik findit'],
                    ],
                ];
            } else {
                return [
                    'reply' => "😊 Kabar baiknya, belum ada laporan barang hilang saat ini!\n\nSemoga kondisi ini tetap terjaga. Kalau barangmu hilang, segera buat laporan ya!",
                    'action' => null,
                    'quickReplies' => $quickReplies,
                ];
            }
        }

        // 6. CARI / SEARCH
        if ($isCari) {
            $msg = "🔎 Untuk mencari barang, kamu bisa:\n\n";
            $msg .= "1️⃣ Kunjungi halaman barang temuan/hilang\n";
            $msg .= "2️⃣ Gunakan fitur pencarian/filter\n";
            $msg .= "3️⃣ Pilih kategori yang sesuai\n\n";
            $msg .= "Atau tanya saya langsung, misalnya:\n";
            $msg .= "• \"barang temuan terbaru\"\n";
            $msg .= "• \"barang hilang terbaru\"\n";
            $msg .= "• \"HP yang hilang\"\n";
            $msg .= "• \"dompet yang ditemukan\"";
            return [
                'reply' => $msg,
                'action' => null,
                'quickReplies' => $quickReplies,
            ];
        }

        // 7. STATISTIK
        if ($isStatistik) {
            $msg = "📊 *Statistik FindIt Campus*\n\n";
            $msg .= "━━━━━━━━━━━━━━━━\n";
            $msg .= "📦 Barang Temuan: *{$totalTemuan}* item\n";
            $msg .= "🔍 Barang Hilang: *{$totalHilang}* item\n";
            $msg .= "✅ Berhasil Dikembalikan: *{$totalSelesai}* item\n";
            $msg .= "━━━━━━━━━━━━━━━━\n\n";
            if ($totalSelesai > 0) {
                $msg .= "🎉 Yay! Sudah ada {$totalSelesai} barang yang berhasil dikembalikan ke pemiliknya!";
            } else {
                $msg .= "Ayo bantu sesama mahasiswa dengan melaporkan barang temuan!";
            }
            return [
                'reply' => $msg,
                'action' => null,
                'quickReplies' => $quickReplies,
            ];
        }

        // 8. KATEGORI
        if ($isKategori) {
            if (!empty($categories)) {
                $msg = "📁 *Kategori Barang*\n\n";
                $msg .= "Saat ini tersedia: {$categories}\n\n";
                $msg .= "Kamu bisa filter barang berdasarkan kategori di halaman pencarian!";
                return [
                    'reply' => $msg,
                    'action' => null,
                    'quickReplies' => $quickReplies,
                ];
            } else {
                return [
                    'reply' => 'Saat ini belum ada kategori yang tersedia.',
                    'action' => null,
                    'quickReplies' => $quickReplies,
                ];
            }
        }

        // 9. CARA LAPOR BARANG HILANG
        if ($isCaraLapor) {
            $msg = "📝 *Cara Membuat Laporan*\n\n";
            $msg .= "1️⃣ Login ke akun FindIt kamu\n";
            $msg .= "2️⃣ Klik menu \"Buat Laporan\"\n";
            $msg .= "3️⃣ Pilih jenis: \"Barang Hilang\"\n";
            $msg .= "4️⃣ Isi formulir:\n";
            $msg .= "   • Nama barang\n";
            $msg .= "   • Kategori\n";
            $msg .= "   • Lokasi terakhir dilihat\n";
            $msg .= "   • Tanggal kehilangan\n";
            $msg .= "   • Deskripsi barang\n";
            $msg .= "5️⃣ Upload foto (jika ada)\n";
            $msg .= "6️⃣ Klik \"Kirim Laporan\"\n\n";
            $msg .= "Laporanmu akan diproses oleh admin!";
            return [
                'reply' => $msg,
                'action' => ['label' => 'Buat Laporan Sekarang', 'url' => route('reports.create')],
                'quickReplies' => [
                    ['label' => '📦 Barang Temuan', 'value' => 'barang temuan terbaru'],
                    ['label' => '🔍 Barang Hilang', 'value' => 'barang hilang terbaru'],
                    ['label' => '🎁 Cara Klaim', 'value' => 'cara klaim barang'],
                ],
            ];
        }

        // 10. CARA KLAIM BARANG TEMUAN
        if ($isCaraKlaim) {
            $msg = "🎁 *Cara Klaim Barang Temuan*\n\n";
            $msg .= "1️⃣ Buka halaman \"Barang Temuan\"\n";
            $msg .= "2️⃣ Pilih barang yang ingin diklaim\n";
            $msg .= "3️⃣ Klik tombol \"Ajukan Klaim\"\n";
            $msg .= "4️⃣ Isi data verifikasi:\n";
            $msg .= "   • Bukti kepemilikan\n";
            $msg .= "   • Deskripsi detail barang\n";
            $msg .= "   • Tanggal & lokasi kehilangan\n";
            $msg .= "5️⃣ Submit klaim\n\n";
            $msg .= "Admin akan memverifikasi klaimmu dalam 1x24 jam!";
            return [
                'reply' => $msg,
                'action' => ['label' => 'Lihat Barang Temuan', 'url' => route('reports.temuan')],
                'quickReplies' => [
                    ['label' => '📦 Barang Temuan', 'value' => 'barang temuan terbaru'],
                    ['label' => '📝 Buat Laporan', 'value' => 'cara buat laporan'],
                    ['label' => '📊 Statistik', 'value' => 'statistik findit'],
                ],
            ];
        }

        // 11. DAFTAR / LIST
        if ($isDaftar) {
            if ($hasTemuan) {
                $msg = "📋 *Daftar Barang Temuan*\n\n";
                $msg .= $temuanTerbaru ?: "Data sedang dimuat...";
                return [
                    'reply' => $msg,
                    'action' => ['label' => 'Lihat Semua', 'url' => route('reports.temuan')],
                    'quickReplies' => [
                        ['label' => '🔍 Barang Hilang', 'value' => 'barang hilang terbaru'],
                        ['label' => '📊 Statistik', 'value' => 'statistik findit'],
                    ],
                ];
            }
            if ($hasHilang) {
                $msg = "📋 *Daftar Barang Hilang*\n\n";
                $msg .= $hilangTerbaru ?: "Data sedang dimuat...";
                return [
                    'reply' => $msg,
                    'action' => ['label' => 'Lihat Semua', 'url' => route('reports.hilang')],
                    'quickReplies' => [
                        ['label' => '📦 Barang Temuan', 'value' => 'barang temuan terbaru'],
                        ['label' => '📊 Statistik', 'value' => 'statistik findit'],
                    ],
                ];
            }
        }

        // 12. TANYA TENTANG APA YANG TERSEDIA/SEDIA
        if ($isSedia) {
            $msg = "✨ *Yang Tersedia di FindIt*\n\n";
            $msg .= "📦 {$totalTemuan} barang temuan\n";
            $msg .= "🔍 {$totalHilang} barang hilang\n";
            $msg .= "✅ {$totalSelesai} barang dikembalikan\n";
            $msg .= "📁 {$categories} kategori\n\n";
            $msg .= "Mau tanya lebih lanjut?";
            return [
                'reply' => $msg,
                'action' => null,
                'quickReplies' => $quickReplies,
            ];
        }

        // 13. DEFAULT - RESPONS TEMPLATE
        $msg = "👋 Halo! Saya asisten FindIt\n\n";
        $msg .= "Saya siap membantu kamu untuk:\n\n";
        $msg .= "━━━━━━━━━━━━━━━━\n";
        $msg .= "📦 *Barang Temuan*: {$totalTemuan} item\n";
        $msg .= "🔍 *Barang Hilang*: {$totalHilang} item\n";
        $msg .= "✅ *Dikembalikan*: {$totalSelesai} item\n";
        $msg .= "━━━━━━━━━━━━━━━━\n\n";
        $msg .= "Silakan pilih opsi di bawah atau ketik pertanyaanmu!";
        return [
            'reply' => $msg,
            'action' => null,
            'quickReplies' => $quickReplies,
        ];
    }

    private function containsAny($text, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }
}
