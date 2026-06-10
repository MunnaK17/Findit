<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINDIT | Sistem Lost& Found Digital Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        headline: ['Manrope', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: '#0F2044',
                        'navy-mid': '#1A3A6B',
                        'navy-light': '#E8EEF8',
                        'navy-pale': '#F0F4FA',
                        accent: '#F5A623',
                        'accent-light': '#FFF5E0',
                        surface: '#F7F9FC',
                        'surface-2': '#FFFFFF',
                        'text-main': '#0F2044',
                        'text-2': '#4A5568',
                        'text-3': '#9AABB8',
                        border: '#DDE3EE',
                    },
                },
            },
        }
    </script>
    <style>
        body {
            background-color: #F7F9FC;
            color: #0F2044;
            font-family: 'Inter', sans-serif;
            padding-top: 62px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .orange-glow {
            box-shadow: 0 8px 32px rgba(245, 166, 35, 0.25);
        }
        .hero-gradient {
            background: radial-gradient(circle at 50% 50%, #E8EEF8 0%, #F7F9FC 100%);
        }
        .step-line::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: repeating-linear-gradient(to right, #F5A623 0, #F5A623 4px, transparent 4px, transparent 8px);
            z-index: -1;
        }
        .step-line:last-child::after { display: none; }
        /* Smooth scroll dengan offset navbar */
        section[id] {
            scroll-margin-top: 70px;
        }
    </style>
</head>
<body class="font-body text-text-main">

    <!-- TopNavBar -->
    <nav id="main-nav" style="background:#0F2044;padding:0 18px;height:62px;display:flex;align-items:center;justify-content:space-between;position:fixed;top:0;left:0;right:0;z-index:50;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
        <a href="#" style="font-size:20px;font-weight:800;color:#fff;text-decoration:none;letter-spacing:-0.5px;flex-shrink:0;font-family:'Manrope',sans-serif;">
            Find<span style="color:#F5A623;">.It</span>
        </a>
        <div style="display:flex;align-items:center;gap:4px;">
            <a href="#home" data-section="home" class="nav-link active" style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;color:#fff;text-decoration:none;background:rgba(255,255,255,0.1);">Beranda</a>
            <a href="#about" data-section="about" class="nav-link" style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;color:rgba(255,255,255,0.65);text-decoration:none;background:transparent;">Tentang Kami</a>
            <a href="#services" data-section="services" class="nav-link" style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;color:rgba(255,255,255,0.65);text-decoration:none;background:transparent;">Layanan</a>
            <a href="#packages" data-section="packages" class="nav-link" style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;color:rgba(255,255,255,0.65);text-decoration:none;background:transparent;">Paket</a>
            <a href="#advantages" data-section="advantages" class="nav-link" style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;color:rgba(255,255,255,0.65);text-decoration:none;background:transparent;">Keunggulan</a>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <a href="#contact" data-section="contact" class="nav-link" style="padding:7px 13px;border-radius:7px;font-size:13px;font-weight:500;color:rgba(255,255,255,0.65);text-decoration:none;background:transparent;">Kontak</a>
            <a href="/app" style="padding:8px 15px;border-radius:8px;font-size:13px;font-weight:700;background:#F5A623;color:#0F2044;text-decoration:none;">Konsultasi Gratis</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient relative pt-24 pb-20 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-accent/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-7xl mx-auto px-5 md:px-10 flex flex-col lg:flex-row items-center gap-12 relative z-10">
            <div class="w-full lg:w-1/2 space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-accent/10 border border-accent/20 text-accent text-xs font-semibold">
                    <span class="material-symbols-outlined text-sm">verified</span>
                    Penyedia Sistem Lost& Found Terpercaya
                </div>
                <h1 class="font-headline text-4xl md:text-5xl lg:text-6xl leading-tight font-extrabold text-navy">
                    Sistem Digital <span class="text-accent">Lost & Found</span> untuk Kampus dan Institusi
                </h1>
                <p class="text-text-2 text-lg leading-relaxed max-w-xl">
                    FINDIT membantu kampus, sekolah, kantor, dan organisasi mengelola laporan barang hilang dan temuan dengan cepat, rapi, dan terpercaya dengan keamanan level institusional.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#contact" class="bg-accent text-navy px-8 py-4 rounded-xl font-bold text-lg hover:shadow-lg hover:shadow-accent/30 transition-all inline-block">
                        Konsultasi Sekarang
                    </a>
                    <a href="#packages" class="bg-white text-navy border-2 border-navy/20 px-8 py-4 rounded-xl font-bold text-lg hover:bg-navy-pale transition-all inline-block">
                        Lihat Paket
                    </a>
                </div>
            </div>
            <div class="w-full lg:w-1/2 relative h-[400px]">
                <!-- Interactive Mockup Scene -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="relative w-full h-full max-w-md">
                        <!-- Found Items Card -->
                        <div class="absolute top-0 -left-6 glass-card p-5 rounded-2xl w-60 shadow-lg rotate-[-4deg] hover:rotate-0 transition-transform duration-500 cursor-default">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                    <span class="material-symbols-outlined">inventory_2</span>
                                </div>
                                <div class="font-bold text-navy">Barang Temuan</div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-2 w-full bg-navy-pale rounded"></div>
                                <div class="h-2 w-2/3 bg-navy-pale rounded"></div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-xs text-text-2">Rekaman Aktif</span>
                                    <span class="text-accent font-bold">75+</span>
                                </div>
                            </div>
                        </div>
                        <!-- Stats Card -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20 bg-navy p-6 rounded-2xl w-64 shadow-xl scale-110">
                            <div class="text-center space-y-3">
                                <div class="text-4xl font-extrabold text-white">120+</div>
                                <div class="text-xs uppercase tracking-widest text-white/60">Barang Tercatat</div>
                                <div class="pt-3 border-t border-white/20 flex justify-around">
                                    <div>
                                        <div class="text-accent font-bold">45+</div>
                                        <div class="text-[10px] uppercase text-white/50">Dikembalikan</div>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold">98%</div>
                                        <div class="text-[10px] uppercase text-white/50">Kepercayaan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Lost Items Card -->
                        <div class="absolute bottom-8 -right-6 glass-card p-5 rounded-2xl w-60 shadow-lg rotate-[4deg] hover:rotate-0 transition-transform duration-500 cursor-default">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-500">
                                    <span class="material-symbols-outlined">report</span>
                                </div>
                                <div class="font-bold text-navy">Laporan Hilang</div>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-full bg-navy-pale"></div>
                                <div class="text-sm font-medium text-navy">Dompet - Kulit Hitam</div>
                            </div>
                            <div class="text-xs text-text-2">Dilaporkan 2 menit lalu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="py-16 md:py-24 bg-white" id="about">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative h-[400px] rounded-3xl overflow-hidden shadow-2xl">
                    <img alt="Tentang FINDIT" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/40 to-transparent"></div>
                </div>
                <div class="space-y-8">
                    <h2 class="font-headline text-3xl md:text-4xl text-navy font-extrabold">Tentang FINDIT</h2>
                    <p class="text-text-2 text-lg leading-relaxed">
                        FINDIT adalah perusahaan yang menyediakan pengembangan sistem informasi berbasis web untuk Lost and Found. Kami mengkhususkan diri dalam mengubah proses manual yang kacau menjadi ekosistem digital yang streamline untuk institusi berskala besar.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-6 bg-navy-pale rounded-xl border border-border hover:border-accent transition-colors">
                            <span class="material-symbols-outlined text-accent text-3xl mb-4 block">bolt</span>
                            <h3 class="font-bold text-navy mb-2">Cepat</h3>
                            <p class="text-sm text-text-2">Pelaporan real-time dan notifikasi instan.</p>
                        </div>
                        <div class="p-6 bg-navy-pale rounded-xl border border-border hover:border-accent transition-colors">
                            <span class="material-symbols-outlined text-accent text-3xl mb-4 block">verified_user</span>
                            <h3 class="font-bold text-navy mb-2">Terpercaya</h3>
                            <p class="text-sm text-text-2">Penanganan data aman untuk barang sensitif.</p>
                        </div>
                        <div class="p-6 bg-navy-pale rounded-xl border border-border hover:border-accent transition-colors">
                            <span class="material-symbols-outlined text-accent text-3xl mb-4 block">touch_app</span>
                            <h3 class="font-bold text-navy mb-2">Mudah</h3>
                            <p class="text-sm text-text-2">UI intuitif untuk admin dan pengguna.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Problems We Solve -->
    <section class="py-16 md:py-24 bg-surface" id="advantages">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <div class="text-center mb-16 space-y-4">
                <h2 class="font-headline text-3xl md:text-4xl text-navy font-extrabold">Masalah Umum Lost & Found</h2>
                <p class="text-text-2 max-w-2xl mx-auto">Sistem tradisional menciptakan frustrasi dan kehilangan data. Kami mengeliminasi bottleneck ini.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-8 bg-white rounded-2xl border-l-4 border-red-400 hover:shadow-lg transition-all duration-300">
                    <span class="material-symbols-outlined text-red-400 text-3xl mb-6 block">error</span>
                    <h4 class="font-bold text-navy text-xl mb-4">Data Tidak Tercatat</h4>
                    <p class="text-text-2 text-sm leading-relaxed">Log manual sering tidak lengkap atau hilang.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border-l-4 border-red-400 hover:shadow-lg transition-all duration-300">
                    <span class="material-symbols-outlined text-red-400 text-3xl mb-6 block">search_off</span>
                    <h4 class="font-bold text-navy text-xl mb-4">Pencarian Manual</h4>
                    <p class="text-text-2 text-sm leading-relaxed">Mencari tanpa henti melalui kotak-kotak fisik.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border-l-4 border-red-400 hover:shadow-lg transition-all duration-300">
                    <span class="material-symbols-outlined text-red-400 text-3xl mb-6 block">query_stats</span>
                    <h4 class="font-bold text-navy text-xl mb-4">Status Kabur</h4>
                    <p class="text-text-2 text-sm leading-relaxed">Pengguna tidak tahu apakah klaimnya sedang diproses.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border-l-4 border-red-400 hover:shadow-lg transition-all duration-300">
                    <span class="material-symbols-outlined text-red-400 text-3xl mb-6 block">analytics</span>
                    <h4 class="font-bold text-navy text-xl mb-4">Masalah Admin</h4>
                    <p class="text-text-2 text-sm leading-relaxed">Kesulitan membuat laporan akhir bulan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FINDIT Solutions -->
    <section class="py-16 md:py-24 bg-white relative overflow-hidden" id="services">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <div class="text-center mb-16">
                <h2 class="font-headline text-3xl md:text-4xl text-navy font-extrabold mb-4">Solusi Digital dari FINDIT</h2>
                <div class="h-1 w-20 bg-accent mx-auto rounded-full"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">assignment_late</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Laporan Barang Hilang</h4>
                        <p class="text-text-2 text-sm">Pelaporan digital cepat dengan dukungan upload foto.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">inventory</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Laporan Barang Temuan</h4>
                        <p class="text-text-2 text-sm">Katalog barang secara instan dengan kategori otomatis.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">filter_alt</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Pencarian & Filter</h4>
                        <p class="text-text-2 text-sm">Pencarian lanjutan berdasarkan tanggal, kategori, atau lokasi.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">visibility</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Detail Barang</h4>
                        <p class="text-text-2 text-sm">Tampilan komprehensif deskripsi dan status barang.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">timeline</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Pelacakan Status</h4>
                        <p class="text-text-2 text-sm">Update real-time verifikasi klaim dan pengembalian.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">dashboard_customize</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Dashboard Admin</h4>
                        <p class="text-text-2 text-sm">Kontrol penuh atas laporan, pengguna, dan konfigurasi.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">manage_accounts</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Manajemen Pengguna</h4>
                        <p class="text-text-2 text-sm">Login aman dan kontrol akses berbasis role.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Riwayat Laporan</h4>
                        <p class="text-text-2 text-sm">Arsip lengkap semua laporan dan keberhasilan.</p>
                    </div>
                </div>
                <div class="flex gap-6 p-6 rounded-2xl hover:bg-navy-pale transition-all group">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-navy flex items-center justify-center text-white group-hover:bg-accent group-hover:text-navy transition-all">
                        <span class="material-symbols-outlined">devices</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-navy text-lg mb-2">Tampilan Responsif</h4>
                        <p class="text-text-2 text-sm">Performa sempurna di desktop, tablet, dan mobile.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Packages -->
    <section class="py-16 md:py-24 bg-surface" id="packages">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <div class="text-center mb-16">
                <h2 class="font-headline text-3xl md:text-4xl text-navy font-extrabold mb-4">Paket Layanan</h2>
                <p class="text-text-2">Solusi yang dapat diskalaikan sesuai dengan jangkauan institusi Anda.</p>
            </div>
            <div class="grid lg:grid-cols-3 gap-8 items-stretch">
                <!-- Basic -->
                <div class="bg-white p-10 rounded-3xl border border-border flex flex-col shadow-sm">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-navy mb-4">Basic Local</h3>
                        <div class="text-3xl font-extrabold text-navy">Rp5.000.000</div>
                    </div>
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Hosting Server Lokal
                        </li>
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Penggunaan Intranet Internal
                        </li>
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Maintenance 3 Bulan
                        </li>
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Dashboard Standar
                        </li>
                    </ul>
                    <a href="#contact" class="w-full py-4 rounded-xl border-2 border-accent text-accent font-bold hover:bg-accent hover:text-navy transition-all text-center block">Mulai Sekarang</a>
                </div>
                <!-- Standard -->
                <div class="bg-navy p-10 rounded-3xl border-2 border-accent relative orange-glow flex flex-col scale-105 z-10 shadow-xl">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-accent text-navy px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest">Paling Direkomendasikan</div>
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-white mb-4">Standard</h3>
                        <div class="text-3xl font-extrabold text-accent">Rp12.000.000</div>
                    </div>
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-center gap-3 text-white">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Hosting Profesional 1 Tahun
                        </li>
                        <li class="flex items-center gap-3 text-white">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Akses Online 24/7
                        </li>
                        <li class="flex items-center gap-3 text-white">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Nama Domain Kustom
                        </li>
                        <li class="flex items-center gap-3 text-white">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Maintenance Premium 1 Tahun
                        </li>
                        <li class="flex items-center gap-3 text-white">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Notifikasi Email
                        </li>
                    </ul>
                    <a href="#contact" class="w-full py-4 rounded-xl bg-accent text-navy font-bold hover:shadow-lg transition-all text-center block">Pilih Paket</a>
                </div>
                <!-- Enterprise -->
                <div class="bg-white p-10 rounded-3xl border border-border flex flex-col shadow-sm">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-navy mb-4">Enterprise</h3>
                        <div class="text-3xl font-extrabold text-navy">Mulai Rp25Juta</div>
                    </div>
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Fitur Fully Custom
                        </li>
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> UI/UX Brand Kustom
                        </li>
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Integrasi Multi-role
                        </li>
                        <li class="flex items-center gap-3 text-text-2">
                            <span class="material-symbols-outlined text-accent text-sm">check_circle</span> Support SLA Prioritas
                        </li>
                    </ul>
                    <a href="#contact" class="w-full py-4 rounded-xl border-2 border-accent text-accent font-bold hover:bg-accent hover:text-navy transition-all text-center block">Kontak Enterprise</a>
                </div>
            </div>
            <p class="text-center mt-12 text-text-2 text-sm italic">Catatan: Harga dapat disesuaikan berdasarkan kebutuhan sistem spesifik dan volume pengguna.</p>
        </div>
    </section>

    <!-- Maintenance Extensions -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <h4 class="text-center font-bold text-navy text-xl mb-10">Perpanjangan Maintenance & Support</h4>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-navy-pale p-6 rounded-2xl flex items-center justify-between border border-border">
                    <div>
                        <div class="font-bold text-navy">Basic Local</div>
                        <div class="text-xs text-text-2">Perpanjangan Tahunan</div>
                    </div>
                    <div class="text-accent font-extrabold">Rp1Juta / tahun</div>
                </div>
                <div class="bg-navy-pale p-6 rounded-2xl flex items-center justify-between border border-border">
                    <div>
                        <div class="font-bold text-navy">Standard</div>
                        <div class="text-xs text-text-2">Perpanjangan Tahunan</div>
                    </div>
                    <div class="text-accent font-extrabold">Rp2,5Juta / tahun</div>
                </div>
                <div class="bg-navy-pale p-6 rounded-2xl flex items-center justify-between border border-border">
                    <div>
                        <div class="font-bold text-navy">Enterprise</div>
                        <div class="text-xs text-text-2">Perpanjangan Tahunan</div>
                    </div>
                    <div class="text-accent font-extrabold">Rp5Juta / tahun</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="py-16 md:py-24 bg-surface">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <h2 class="text-center font-headline text-3xl md:text-4xl text-navy font-extrabold mb-16">Alur Kerja yang Dioptimalkan</h2>
            <div class="flex flex-col md:flex-row justify-between items-start gap-12 relative">
                <div class="flex-1 text-center relative step-line w-full">
                    <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-6 relative z-10 border-2 border-accent text-accent">
                        <span class="material-symbols-outlined text-2xl text-white">edit_note</span>
                    </div>
                    <div class="text-accent font-bold text-sm mb-2">LANGKAH 01</div>
                    <h4 class="text-xl font-bold text-navy mb-4">Lapor</h4>
                    <p class="text-text-2 text-sm px-4">Admin atau pengguna mengajukan detail barang.</p>
                </div>
                <div class="flex-1 text-center relative step-line w-full">
                    <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-6 relative z-10 border-2 border-accent text-accent">
                        <span class="material-symbols-outlined text-2xl text-white">archive</span>
                    </div>
                    <div class="text-accent font-bold text-sm mb-2">LANGKAH 02</div>
                    <h4 class="text-xl font-bold text-navy mb-4">Simpan</h4>
                    <p class="text-text-2 text-sm px-4">Sistem menyimpan barang di database.</p>
                </div>
                <div class="flex-1 text-center relative step-line w-full">
                    <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-6 relative z-10 border-2 border-accent text-accent">
                        <span class="material-symbols-outlined text-2xl text-white">fact_check</span>
                    </div>
                    <div class="text-accent font-bold text-sm mb-2">LANGKAH 03</div>
                    <h4 class="text-xl font-bold text-navy mb-4">Verifikasi</h4>
                    <p class="text-text-2 text-sm px-4">Verifikasi klaim aman oleh staff.</p>
                </div>
                <div class="flex-1 text-center relative step-line w-full">
                    <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-6 relative z-10 border-2 border-accent text-accent">
                        <span class="material-symbols-outlined text-2xl text-white">handshake</span>
                    </div>
                    <div class="text-accent font-bold text-sm mb-2">LANGKAH 04</div>
                    <h4 class="text-xl font-bold text-navy mb-4">Kembalikan</h4>
                    <p class="text-text-2 text-sm px-4">Barang berhasil disatukan kembali dengan pemilik.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Case Study -->

    <!-- Final CTA -->
   
    <footer class="bg-navy py-12 md:py-20 px-5 md:px-10 flex flex-col gap-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto w-full flex flex-col md:flex-row justify-between gap-12">
            <div class="md:w-1/3 space-y-6">
                <div class="text-2xl font-headline font-extrabold text-white">
                    FIND<span class="text-accent">IT</span>
                </div>
                <p class="text-white/60 text-sm leading-relaxed">
                    Penyedia terkemuka ekosistem digital lost and found berperforma tinggi yang dirancang untuk institusi yang menuntut keandalan dan presisi.
                </p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/60 hover:text-accent hover:bg-white/20 transition-colors" href="#">
                        <span class="material-symbols-outlined text-xl">public</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/60 hover:text-accent hover:bg-white/20 transition-colors" href="#">
                        <span class="material-symbols-outlined text-xl">mail</span>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-2 gap-12 md:w-2/3">
                <div class="space-y-6">
                    <h5 class="font-bold text-accent uppercase text-xs tracking-widest">Navigasi</h5>
                    <ul class="space-y-4">
                        <li><a class="text-white/60 hover:text-accent transition-colors text-sm" href="#">Beranda</a></li>
                        <li><a class="text-white/60 hover:text-accent transition-colors text-sm" href="#about">Tentang Kami</a></li>
                        <li><a class="text-white/60 hover:text-accent transition-colors text-sm" href="#services">Layanan</a></li>
                        <li><a class="text-white/60 hover:text-accent transition-colors text-sm" href="#packages">Paket</a></li>
                    </ul>
                </div>
                <div class="space-y-6">
                    <h5 class="font-bold text-accent uppercase text-xs tracking-widest">Informasi Kontak</h5>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-white/60 text-sm">
                            <span class="material-symbols-outlined text-accent">email</span>
                            findit.company@gmail.com
                        </li>
                        <li class="flex items-center gap-3 text-white/60 text-sm">
                            <span class="material-symbols-outlined text-accent">phone</span>
                            08xx-xxxx-xxxx
                        </li>
                        <li class="flex items-center gap-3 text-white/60 text-sm">
                            <span class="material-symbols-outlined text-accent">location_on</span>
                            Jakarta, Indonesia
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto w-full pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs text-white/40">© 2026 FINDIT Sistem Lost and Found. Hak cipta dilindungi.</p>
            <div class="flex gap-6">
                <a class="text-xs text-white/40 hover:text-accent transition-colors" href="#">Kebijakan Privasi</a>
                <a class="text-xs text-white/40 hover:text-accent transition-colors" href="#">Ketentuan Layanan</a>
                <a class="text-xs text-white/40 hover:text-accent transition-colors" href="#">Keamanan</a>
            </div>
        </div>
    </footer>

    <script>
        // Active nav link based on scroll position
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');

        function setActiveNav() {
            let current = '';
            const scrollY = window.scrollY;

            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');

                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    current = sectionId;
                }
            });

            navLinks.forEach(link => {
                const section = link.getAttribute('data-section');
                if (section === current) {
                    link.style.background = 'rgba(255,255,255,0.1)';
                    link.style.color = '#fff';
                } else {
                    link.style.background = 'transparent';
                    link.style.color = 'rgba(255,255,255,0.65)';
                }
            });
        }

        window.addEventListener('scroll', setActiveNav);
        setActiveNav();

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>