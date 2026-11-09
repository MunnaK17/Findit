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
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .detail-box { background: #f9fafb; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .detail-box strong { color: #111827; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 16px; }
        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔔 FindIT - Lost& Found</h1>
    </div>
    <div class="body">
        @if($status === 'pending')
            <p>Hi <strong>{{ $claim->user->name }}</strong>,</p>
            <p>Klaim kamu untuk barang <strong>{{ $report->nama_barang }}</strong> telah kami terima dan sedang menunggu verifikasi admin.</p>
            <div class="detail-box">
                <strong>Detail Klaim:</strong><br>
                📦 Barang: {{ $report->nama_barang }}<br>
                📍 Lokasi: {{ $report->lokasi }}<br>
                📅 Tanggal: {{ \Carbon\Carbon::parse($report->tanggal_kejadian)->format('d M Y') }}<br>
                📝 Pesan: {{ $claim->pesan_klaim }}
            </div>
        @elseif($status === 'approved')
            <p>🎉 Selamat <strong>{{ $claim->user->name }}</strong>!</p>
            <p>Klaim kamu untuk barang <strong>{{ $report->nama_barang }}</strong> telah <span class="status-badge status-approved">DISETUJI</span> oleh admin.</p>
            <p>Silakan datang ke admin kampus di lobby untuk mengambil barang Anda. Tunjukkan email notifikasi ini sebagai bukti pengambilan.</p>
            <div class="detail-box">
                <strong>Detail Barang:</strong><br>
                📦 Nama: {{ $report->nama_barang }}<br>
                📍 Lokasi Ditemukan: {{ $report->lokasi }}<br>
                📅 Tanggal: {{ \Carbon\Carbon::parse($report->tanggal_kejadian)->format('d M Y') }}
            </div>
        @elseif($status === 'rejected')
            <p>Hai <strong>{{ $claim->user->name }}</strong>,</p>
            <p>Mohon maaf, klaim kamu untuk barang <strong>{{ $report->nama_barang }}</strong> telah <span class="status-badge status-rejected">DITOLAK</span>.</p>
            @if($adminNote)
            <div class="detail-box">
                <strong>Catatan Admin:</strong><br>
                {{ $adminNote }}
            </div>
            @endif
        @endif
        <a href="{{ url('/barang/' . $report->id) }}" class="btn">Lihat Detail Barang</a>
    </div>
    <div class="footer">
        FindIT - Lost & Found System &bull; Universitas Indonesia<br>
        Email ini dikirim otomatis, mohon jangan dibalas.
    </div>
</div>
</body>
</html>
