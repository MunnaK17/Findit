<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'FindIT Notification' }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 30px; color: #374151; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 999px; font-size: 14px; font-weight: 600; }
        .status-hilang { background: #fee2e2; color: #991b1b; }
        .status-temuan { background: #d1fae5; color: #065f46; }
        .detail-box { background: #f9fafb; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .detail-box strong { color: #111827; }
        .info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-box strong { color: #1e40af; }
        .info-box .time { font-size: 18px; font-weight: 700; color: #1e40af; }
        .step { margin: 12px 0; padding: 12px; background: #fff; border-radius: 6px; border-left: 4px solid #2563eb; }
        .step strong { color: #1e40af; }
        .warning-box { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 12px; margin-top: 12px; }
        .warning-box strong { color: #92400e; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 16px; }
        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📋 FindIT - Lost & Found</h1>
    </div>
    <div class="body">
        @if($jenis === 'temuan')
            <p>Hi <strong>{{ $report->user->name }}</strong>,</p>
            <p>Terima kasih telah melaporkan temuan barang! Laporan Anda telah kami terima.</p>
            <div class="detail-box">
                <strong>📦 Detail Barang Temuan:</strong><br>
                Nama: {{ $report->nama_barang }}<br>
                Kategori: {{ $report->category->nama_category ?? '-' }}<br>
                Lokasi: {{ $report->lokasi }}<br>
                Tanggal: {{ \Carbon\Carbon::parse($report->tanggal_kejadian)->format('d M Y') }}<br>
                @if($report->deskripsi)
                Deskripsi: {{ Str::limit($report->deskripsi, 100) }}
                @endif
            </div>
            <div class="info-box">
                <strong>📌 Langkah Selanjutnya - Serahkan Barang ke Resepsionis:</strong><br><br>
                <div class="step"><strong>Langkah 1:</strong> Bawa barang temuan Anda ke resepsionis kampus<br><strong>Lokasi:</strong> Lobby utama kampus</div>
                <div class="step"><strong>Langkah 2:</strong> Serahkan barang beserta bukti laporan ini<br><strong>Waktu:</strong> <span class="time">Jam 06:30 - 10:00</span></div>
                <div class="step"><strong>Langkah 3:</strong> Resepsionis akan mencatat dan menyimpan barang temuan Anda</div>
                <div class="warning-box"><strong>⚠️ Penting:</strong> Setelah diserahkan, laporan Anda akan diverifikasi oleh admin dan akan tampil di halaman barang temuan kampus.</div>
            </div>
            <p>Tunjukkan email notifikasi ini sebagai bukti saat menyerahkan barang ke resepsionis.</p>
        @elseif($jenis === 'hilang')
            <p>Hai <strong>{{ $report->user->name }}</strong>,</p>
            <p>Laporan Anda telah kami terima dan sedang <span class="status-badge status-hilang">MENUNGGU VERIFIKASI</span> dari admin.</p>
            <div class="detail-box">
                <strong>📦 Detail Barang Hilang:</strong><br>
                Nama: {{ $report->nama_barang }}<br>
                Kategori: {{ $report->category->nama_category ?? '-' }}<br>
                Lokasi: {{ $report->lokasi }}<br>
                Tanggal: {{ \Carbon\Carbon::parse($report->tanggal_kejadian)->format('d M Y') }}<br>
                @if($report->deskripsi)
                Deskripsi: {{ Str::limit($report->deskripsi, 100) }}
                @endif
            </div>
            <div class="info-box">
                <strong>📌 Apa yang terjadi selanjutnya?</strong><br>
                • Tim admin akan memverifikasi laporan Anda<br>
                • Jika ada barang yang cocok ditemukan, kami akan menghubungi Anda<br>
                • Anda dapat memantau status laporan di halaman "Laporan Saya"
            </div>
            <p>Kami akan segera menghubungi Anda jika ada perkembangan. Terima kasih atas kesabaran Anda!</p>
        @endif
        <a href="{{ url('/my/reports') }}" class="btn">Lihat Laporan Saya</a>
    </div>
    <div class="footer">
        FindIT - Lost & Found System &bull; Universitas Indonesia<br>
        Email ini dikirim otomatis, mohon jangan dibalas.
    </div>
</div>
</body>
</html>
