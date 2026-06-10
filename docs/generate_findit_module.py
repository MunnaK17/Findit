from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    BaseDocTemplate,
    Frame,
    PageBreak,
    PageTemplate,
    Paragraph,
    Preformatted,
    Spacer,
    Table,
    TableStyle,
)
from reportlab.platypus.tableofcontents import TableOfContents


ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / "docs" / "Modul-FindIt-Lost-and-Found.pdf"


class ModuleDocTemplate(BaseDocTemplate):
    def __init__(self, filename: str):
        super().__init__(
            filename,
            pagesize=A4,
            leftMargin=2.1 * cm,
            rightMargin=2.1 * cm,
            topMargin=2.2 * cm,
            bottomMargin=1.9 * cm,
        )
        frame = Frame(
            self.leftMargin,
            self.bottomMargin,
            self.width,
            self.height,
            id="normal",
        )
        self.addPageTemplates(
            PageTemplate(id="body", frames=[frame], onPage=self._header_footer)
        )

    def afterFlowable(self, flowable):
        if isinstance(flowable, Paragraph):
            text = flowable.getPlainText()
            style_name = flowable.style.name
            if style_name == "WeekTitle":
                self.notify("TOCEntry", (0, text, self.page))

    def _header_footer(self, canvas, doc):
        page = doc.page
        canvas.saveState()
        if page > 1:
            canvas.setFont("Helvetica-Bold", 8.5)
            canvas.setFillColor(colors.HexColor("#1F3A5F"))
            canvas.drawString(doc.leftMargin, A4[1] - 1.35 * cm, "MODUL FINDIT LOST AND FOUND")
            canvas.setFont("Helvetica", 8.5)
            canvas.setFillColor(colors.HexColor("#4B5563"))
            canvas.drawRightString(
                A4[0] - doc.rightMargin,
                A4[1] - 1.35 * cm,
                str(page - 1),
            )
            canvas.setStrokeColor(colors.HexColor("#CBD5E1"))
            canvas.line(doc.leftMargin, A4[1] - 1.55 * cm, A4[0] - doc.rightMargin, A4[1] - 1.55 * cm)
        canvas.restoreState()


def styles():
    base = getSampleStyleSheet()
    return {
        "cover_title": ParagraphStyle(
            "CoverTitle",
            parent=base["Title"],
            alignment=TA_CENTER,
            fontName="Helvetica-Bold",
            fontSize=24,
            leading=31,
            textColor=colors.HexColor("#0F2044"),
            spaceAfter=12,
        ),
        "cover_sub": ParagraphStyle(
            "CoverSub",
            parent=base["Normal"],
            alignment=TA_CENTER,
            fontName="Helvetica-Bold",
            fontSize=14,
            leading=19,
            textColor=colors.HexColor("#1F3A5F"),
        ),
        "normal": ParagraphStyle(
            "NormalFindIt",
            parent=base["BodyText"],
            fontName="Helvetica",
            fontSize=10.2,
            leading=15,
            alignment=TA_JUSTIFY,
            spaceAfter=7,
        ),
        "small": ParagraphStyle(
            "SmallFindIt",
            parent=base["BodyText"],
            fontName="Helvetica",
            fontSize=8.8,
            leading=12,
            textColor=colors.HexColor("#374151"),
        ),
        "week": ParagraphStyle(
            "WeekTitle",
            parent=base["Heading1"],
            fontName="Helvetica-Bold",
            fontSize=17,
            leading=22,
            textColor=colors.HexColor("#0F2044"),
            spaceBefore=2,
            spaceAfter=12,
        ),
        "section": ParagraphStyle(
            "SectionTitle",
            parent=base["Heading2"],
            fontName="Helvetica-Bold",
            fontSize=12.2,
            leading=16,
            textColor=colors.HexColor("#1F3A5F"),
            spaceBefore=9,
            spaceAfter=6,
        ),
        "code": ParagraphStyle(
            "CodeBlock",
            parent=base["Code"],
            fontName="Courier",
            fontSize=7.8,
            leading=10,
            leftIndent=0,
            rightIndent=0,
            textColor=colors.HexColor("#111827"),
            backColor=colors.HexColor("#F3F4F6"),
            borderPadding=6,
            borderColor=colors.HexColor("#D1D5DB"),
            borderWidth=0.4,
            spaceBefore=4,
            spaceAfter=8,
        ),
        "center": ParagraphStyle(
            "Center",
            parent=base["BodyText"],
            alignment=TA_CENTER,
            fontSize=10,
            leading=14,
        ),
        "bullet": ParagraphStyle(
            "Bullet",
            parent=base["BodyText"],
            fontName="Helvetica",
            fontSize=10,
            leading=14,
            leftIndent=14,
            firstLineIndent=-9,
            spaceAfter=4,
        ),
    }


S = styles()


def p(text, style="normal"):
    return Paragraph(text, S[style])


def bullets(items):
    flow = []
    for item in items:
        flow.append(p(f"- {item}", "bullet"))
    return flow


def code(text):
    return Preformatted(text.strip(), S["code"])


def table(rows, col_widths=None):
    if col_widths is None:
        col_widths = [4.2 * cm, 11.2 * cm]
    t = Table(rows, colWidths=col_widths, hAlign="LEFT")
    t.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#0F2044")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("FONTNAME", (0, 1), (-1, -1), "Helvetica"),
                ("FONTSIZE", (0, 0), (-1, -1), 8.8),
                ("LEADING", (0, 0), (-1, -1), 12),
                ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#CBD5E1")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("BACKGROUND", (0, 1), (-1, -1), colors.HexColor("#F8FAFC")),
                ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
                ("LEFTPADDING", (0, 0), (-1, -1), 6),
                ("RIGHTPADDING", (0, 0), (-1, -1), 6),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
            ]
        )
    )
    return t


def week(title, tujuan, materi, praktik, kode=None, latihan=None):
    flow = [PageBreak(), p(title, "week")]
    flow.append(p("Target Pembelajaran", "section"))
    flow.extend(bullets(tujuan))
    flow.append(p("Pembahasan Materi", "section"))
    for item in materi:
        flow.append(p(item))
    flow.append(p("Langkah Praktikum", "section"))
    flow.extend(bullets(praktik))
    if kode:
        flow.append(p("Contoh Potongan Kode", "section"))
        flow.append(code(kode))
    if latihan:
        flow.append(p("Tugas Praktikum", "section"))
        flow.extend(bullets(latihan))
    return flow


def build_story():
    story = []

    story.extend(
        [
            Spacer(1, 2.8 * cm),
            p("MODUL", "cover_sub"),
            Spacer(1, 0.2 * cm),
            p("PENGEMBANGAN WEB APP<br/>LOST AND FOUND FINDIT", "cover_title"),
            Spacer(1, 0.4 * cm),
            p("Studi Kasus Project Kampus UBSI Slipi", "cover_sub"),
            Spacer(1, 6.1 * cm),
            p("FAKULTAS TEKNIK DAN INFORMATIKA", "cover_sub"),
            p("UNIVERSITAS BINA SARANA INFORMATIKA", "cover_sub"),
            p("2026", "cover_sub"),
            PageBreak(),
        ]
    )

    story.append(p("KATA PENGANTAR", "week"))
    story.append(
        p(
            "Segala puji dan syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa, "
            "karena modul pengembangan aplikasi FindIt ini dapat disusun sebagai bahan "
            "pembelajaran praktikum pembuatan web app Lost and Found berbasis Laravel."
        )
    )
    story.append(
        p(
            "Modul ini menggunakan studi kasus FindIt, yaitu platform pelaporan barang "
            "hilang dan barang temuan di lingkungan kampus UBSI Slipi. Materi disusun "
            "secara bertahap mulai dari pengenalan struktur project, perancangan database, "
            "autentikasi, fitur laporan, fitur klaim, halaman admin, sampai integrasi chatbot."
        )
    )
    story.append(
        p(
            "Setiap pertemuan dilengkapi target pembelajaran, pembahasan konsep, langkah "
            "praktikum, potongan kode, dan tugas. Dengan pola tersebut mahasiswa diharapkan "
            "mampu memahami hubungan antara route, controller, model, migration, dan view "
            "pada aplikasi Laravel yang nyata."
        )
    )
    story.append(Spacer(1, 0.8 * cm))
    story.append(p("Jakarta, 4 Juni 2026", "normal"))
    story.append(p("Tim Penyusun", "normal"))
    story.append(PageBreak())

    story.append(p("DAFTAR ISI", "week"))
    toc = TableOfContents()
    toc.levelStyles = [
        ParagraphStyle(
            fontName="Helvetica",
            fontSize=10,
            name="TOCHeading1",
            leftIndent=0,
            firstLineIndent=0,
            spaceBefore=5,
            leading=13,
        ),
        ParagraphStyle(
            fontName="Helvetica",
            fontSize=8.8,
            name="TOCHeading2",
            leftIndent=14,
            firstLineIndent=0,
            spaceBefore=2,
            leading=11,
            textColor=colors.HexColor("#475569"),
        ),
    ]
    story.append(toc)

    story.append(PageBreak())
    story.append(p("Gambaran Umum Project", "week"))
    story.append(
        p(
            "FindIt adalah aplikasi web Lost and Found untuk membantu mahasiswa melaporkan "
            "barang hilang, mengumumkan barang temuan, dan mengajukan klaim atas barang "
            "yang ditemukan. Aplikasi ini memiliki dua area utama, yaitu area mahasiswa dan "
            "area administrator."
        )
    )
    story.append(p("Identitas Aplikasi", "section"))
    story.append(
        table(
            [
                ["Komponen", "Keterangan"],
                ["Nama aplikasi", "FindIt"],
                ["Jenis aplikasi", "Website Lost and Found kampus"],
                ["Lokasi studi kasus", "Kampus UBSI Slipi"],
                ["Framework", "Laravel 13, PHP 8.3, Laravel Breeze, Blade"],
                ["Frontend", "Tailwind, Bootstrap 5, Vite"],
                ["Database utama", "users, categories, reports, claims"],
                ["Role pengguna", "mahasiswa dan admin"],
            ]
        )
    )
    story.append(p("Alur Sistem", "section"))
    story.extend(
        bullets(
            [
                "Mahasiswa melakukan registrasi atau login.",
                "Mahasiswa membuat laporan barang hilang atau barang temuan.",
                "Admin melakukan verifikasi laporan dengan status pending, approved, rejected, atau completed.",
                "Laporan approved tampil pada halaman publik barang hilang dan barang temuan.",
                "Mahasiswa dapat mengajukan klaim hanya pada laporan barang temuan yang approved.",
                "Jika klaim disetujui admin, laporan otomatis berubah menjadi completed.",
            ]
        )
    )

    weeks = [
        (
            "Minggu Ke-1  Pengenalan Project FindIt dan Struktur Laravel",
            [
                "Mahasiswa memahami tujuan aplikasi FindIt.",
                "Mahasiswa mampu membaca struktur folder Laravel.",
                "Mahasiswa mengenal relasi MVC pada project.",
            ],
            [
                "Project FindIt berada pada direktori c:\\laragon\\www\\findit. Struktur utamanya mengikuti pola Laravel, yaitu folder app untuk model, controller, dan middleware; folder routes untuk definisi URL; folder resources untuk Blade view; serta folder database untuk migration dan seeder.",
                "Pada aplikasi ini, pola MVC terlihat dari route yang memanggil controller, controller mengambil data melalui model, lalu mengirim data ke file Blade untuk ditampilkan.",
            ],
            [
                "Buka file composer.json untuk melihat versi Laravel dan dependensi backend.",
                "Buka file package.json untuk melihat dependensi frontend.",
                "Catat folder app/Models, app/Http/Controllers, routes, resources/views, dan database/migrations.",
            ],
            """app/
  Http/Controllers/
  Models/
database/
  migrations/
  seeders/
resources/
  views/
routes/
  web.php""",
            [
                "Jelaskan fungsi folder routes, app/Models, dan resources/views.",
                "Buat diagram sederhana hubungan route, controller, model, dan view.",
            ],
        ),
        (
            "Minggu Ke-2  Perancangan Database Lost and Found",
            [
                "Mahasiswa memahami tabel yang digunakan FindIt.",
                "Mahasiswa mampu menjelaskan relasi user, category, report, dan claim.",
                "Mahasiswa dapat membaca migration Laravel.",
            ],
            [
                "Database FindIt memiliki tabel users, categories, reports, dan claims. Tabel users menyimpan akun mahasiswa dan admin. Tabel categories menyimpan kategori barang. Tabel reports menyimpan laporan barang hilang atau temuan. Tabel claims menyimpan pengajuan klaim atas barang temuan.",
                "Setiap report dimiliki oleh satu user dan satu category. Setiap claim dimiliki oleh satu user dan satu report.",
            ],
            [
                "Buka migration create_categories_table.",
                "Buka migration create_reports_table dan perhatikan enum jenis_laporan serta status.",
                "Buka migration create_claims_table dan perhatikan status_klaim.",
            ],
            """Schema::create('reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
    $table->foreignId('id_category')->constrained('categories')->onDelete('cascade');
    $table->enum('jenis_laporan', ['hilang', 'temuan']);
    $table->string('nama_barang');
    $table->text('deskripsi');
    $table->string('lokasi');
    $table->date('tanggal_kejadian');
    $table->string('foto_barang')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
    $table->timestamps();
});""",
            [
                "Buat ERD sederhana untuk tabel users, categories, reports, dan claims.",
                "Jelaskan perbedaan status laporan dan status klaim.",
            ],
        ),
        (
            "Minggu Ke-3  Model dan Relasi Eloquent",
            [
                "Mahasiswa memahami fillable, casts, dan relasi Eloquent.",
                "Mahasiswa mampu membaca relasi belongsTo dan hasMany.",
            ],
            [
                "Model Report menggunakan relasi belongsTo ke User dan Category, serta hasMany ke Claim. Model Claim menggunakan relasi belongsTo ke Report dan User. Relasi ini memudahkan controller mengambil data lengkap tanpa query manual yang berulang.",
                "Helper seperti bisaDiklaim(), statusBadge(), dan jenisLabel() digunakan untuk menyimpan aturan kecil yang sering dipakai pada tampilan.",
            ],
            [
                "Buka app/Models/Report.php dan catat field fillable.",
                "Buka app/Models/Claim.php dan catat relasi ke report dan user.",
                "Uji konsep eager loading dengan with(['category', 'user']).",
            ],
            """class Report extends Model
{
    use HasFactory, SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class, 'id_report');
    }
}""",
            [
                "Tuliskan contoh penggunaan $report->category->nama_category pada Blade.",
                "Jelaskan mengapa eager loading dapat mengurangi query berulang.",
            ],
        ),
        (
            "Minggu Ke-4  Routing Aplikasi FindIt",
            [
                "Mahasiswa mampu membaca route publik, auth, dan admin.",
                "Mahasiswa memahami penggunaan middleware auth dan admin.",
            ],
            [
                "Route publik digunakan untuk halaman beranda, daftar barang hilang, daftar barang temuan, dan detail barang. Route mahasiswa dibungkus middleware auth. Route admin dibungkus middleware auth dan admin, memakai prefix admin dan name admin.",
                "Urutan route juga penting. Pada klaim, route /klaim/saya diletakkan sebelum /klaim/{reportId}/ajukan agar tidak tertangkap sebagai parameter dinamis.",
            ],
            [
                "Buka routes/web.php.",
                "Kelompokkan route berdasarkan akses public, auth user, dan admin.",
                "Jalankan php artisan route:list untuk melihat seluruh daftar route.",
            ],
            """Route::get('/barang-hilang', [ReportController::class, 'hilang'])
    ->name('reports.hilang');

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan/buat', [ReportController::class, 'create'])
        ->name('reports.create');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    });""",
            [
                "Buat tabel route berisi URL, controller, method, dan hak akses.",
                "Jelaskan alasan penggunaan named route pada Blade.",
            ],
        ),
        (
            "Minggu Ke-5  Autentikasi, Role, dan Middleware Admin",
            [
                "Mahasiswa memahami autentikasi Laravel Breeze.",
                "Mahasiswa mampu menjelaskan role mahasiswa dan admin.",
                "Mahasiswa memahami custom middleware admin.",
            ],
            [
                "FindIt menggunakan Laravel Breeze untuk login, register, profile, dan logout. Tabel users ditambah kolom nim dan role. Role default adalah mahasiswa, sedangkan admin dibuat melalui seeder.",
                "Middleware AdminMiddleware memastikan hanya user dengan role admin yang dapat membuka halaman admin.",
            ],
            [
                "Buka migration add_nim_role_to_users_table.",
                "Buka database/seeders/AdminSeeder.php.",
                "Buka app/Http/Middleware/AdminMiddleware.php.",
            ],
            """class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus admin.');
        }

        return $next($request);
    }
}""",
            [
                "Coba login sebagai mahasiswa dan akses /admin/dashboard.",
                "Jelaskan perbedaan auth middleware dan admin middleware.",
            ],
        ),
        (
            "Minggu Ke-6  Halaman Beranda dan Layout Utama",
            [
                "Mahasiswa memahami layout Blade menggunakan komponen x-app-layout.",
                "Mahasiswa mampu menampilkan data laporan terbaru di beranda.",
            ],
            [
                "Halaman beranda menampilkan hero FindIt, statistik barang temuan, barang hilang, dan barang yang selesai. Data diambil dari HomeController melalui query report approved terbaru.",
                "Layout utama berada pada resources/views/layouts/app.blade.php dan memuat navbar, flash message, konten slot, footer, serta chatbot widget untuk user login.",
            ],
            [
                "Buka HomeController.php.",
                "Buka resources/views/home.blade.php.",
                "Perhatikan pemakaian route('reports.temuan') dan route('reports.create').",
            ],
            """public function index()
{
    $laporanTemuan = Report::with(['category', 'user'])
        ->where('jenis_laporan', 'temuan')
        ->where('status', 'approved')
        ->latest()
        ->take(6)
        ->get();

    $laporanHilang = Report::with(['category', 'user'])
        ->where('jenis_laporan', 'hilang')
        ->where('status', 'approved')
        ->latest()
        ->take(6)
        ->get();

    return view('home', compact('laporanTemuan', 'laporanHilang'));
}""",
            [
                "Tambahkan satu statistik baru pada hero, misalnya total kategori.",
                "Jelaskan kenapa hanya laporan approved yang ditampilkan ke publik.",
            ],
        ),
        (
            "Minggu Ke-7  Fitur Daftar Barang Hilang dan Barang Temuan",
            [
                "Mahasiswa memahami filter pencarian, kategori, dan sorting.",
                "Mahasiswa mampu menggunakan pagination pada Laravel.",
            ],
            [
                "ReportController memiliki method hilang() dan temuan(). Keduanya memiliki pola query yang mirip: mengambil report berdasarkan jenis_laporan, status approved, filter search, filter category, sorting latest atau oldest, lalu paginate.",
                "Fitur ini menjadi halaman utama bagi mahasiswa yang ingin mencari barang.",
            ],
            [
                "Buka method hilang() dan temuan() di ReportController.",
                "Amati query where, orWhere, latest, oldest, dan paginate.",
                "Buka view reports/hilang.blade.php dan reports/temuan.blade.php.",
            ],
            """if ($request->search) {
    $search = '%' . $request->search . '%';
    $query->where(function ($q) use ($search) {
        $q->where('nama_barang', 'like', $search)
          ->orWhere('deskripsi', 'like', $search)
          ->orWhere('lokasi', 'like', $search);
    });
}

if ($request->category) {
    $query->where('id_category', $request->category);
}""",
            [
                "Tambahkan filter lokasi pada halaman daftar barang.",
                "Uji pencarian berdasarkan nama barang dan lokasi.",
            ],
        ),
        (
            "Minggu Ke-8  Membuat Laporan Barang Hilang atau Temuan",
            [
                "Mahasiswa mampu membuat form input laporan.",
                "Mahasiswa memahami validasi request dan upload foto.",
                "Mahasiswa memahami status awal pending.",
            ],
            [
                "Mahasiswa membuat laporan melalui /laporan/buat. Form berisi kategori, jenis laporan, nama barang, deskripsi, lokasi, tanggal kejadian, dan foto barang. Controller melakukan validasi sebelum data disimpan.",
                "Foto disimpan ke disk public pada folder foto_barang. Agar gambar dapat tampil di browser, storage link Laravel harus tersedia.",
            ],
            [
                "Buka ReportController method create() dan store().",
                "Buka view reports/create.blade.php.",
                "Pastikan form memakai enctype multipart/form-data untuk upload file.",
            ],
            """$request->validate([
    'id_category' => ['required', 'exists:categories,id'],
    'jenis_laporan' => ['required', 'in:hilang,temuan'],
    'nama_barang' => ['required', 'string', 'max:255'],
    'deskripsi' => ['required', 'string', 'min:20'],
    'lokasi' => ['required', 'string', 'max:255'],
    'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
    'foto_barang' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
]);""",
            [
                "Buat laporan barang hilang dan barang temuan menggunakan akun mahasiswa.",
                "Cek apakah laporan masuk ke menu Laporan Saya dengan status pending.",
            ],
        ),
        (
            "Minggu Ke-9  Manajemen Laporan Saya",
            [
                "Mahasiswa memahami pembatasan akses data milik sendiri.",
                "Mahasiswa mampu membaca fitur edit, update, dan delete laporan.",
            ],
            [
                "Menu Laporan Saya menampilkan report milik user yang sedang login. Query selalu menggunakan id_user Auth::id() sehingga mahasiswa tidak dapat melihat atau mengubah laporan milik user lain.",
                "Edit dan hapus hanya diperbolehkan ketika laporan masih pending. Jika sudah approved, rejected, atau completed, laporan tidak dapat diubah oleh mahasiswa.",
            ],
            [
                "Buka method myReports(), edit(), update(), dan destroy().",
                "Perhatikan penggunaan Report::where('id_user', Auth::id()).",
                "Uji edit laporan pending dan laporan approved.",
            ],
            """$report = Report::where('id_user', Auth::id())->findOrFail($id);

if ($report->status !== 'pending') {
    return redirect()->route('my.reports')
        ->with('error', 'Laporan yang sudah diproses tidak dapat diedit.');
}""",
            [
                "Jelaskan mengapa findOrFail dipakai setelah where id_user.",
                "Tambahkan catatan tampilan untuk laporan yang ditolak admin.",
            ],
        ),
        (
            "Minggu Ke-10  Fitur Klaim Barang Temuan",
            [
                "Mahasiswa memahami alur pengajuan klaim.",
                "Mahasiswa mampu menerapkan validasi agar klaim tidak duplikat.",
                "Mahasiswa memahami aturan tidak boleh klaim laporan sendiri.",
            ],
            [
                "Klaim hanya dapat diajukan pada laporan jenis temuan dengan status approved. Controller mengecek apakah user sudah pernah mengajukan klaim dan apakah report tersebut milik user sendiri.",
                "Setiap klaim berisi pesan klaim sebagai bukti atau penjelasan kepemilikan barang.",
            ],
            [
                "Buka ClaimController method create() dan store().",
                "Buka view claims/create.blade.php.",
                "Ajukan klaim dari user berbeda untuk sebuah barang temuan.",
            ],
            """$report = Report::where('jenis_laporan', 'temuan')
    ->where('status', 'approved')
    ->findOrFail($reportId);

if ($report->id_user === Auth::id()) {
    return redirect()->route('reports.show', $reportId)
        ->with('error', 'Kamu tidak bisa mengklaim laporan milikmu sendiri.');
}""",
            [
                "Buat skenario uji: klaim barang sendiri, klaim barang orang lain, dan klaim ganda.",
                "Jelaskan fungsi status_klaim pending, approved, dan rejected.",
            ],
        ),
        (
            "Minggu Ke-11  Dashboard dan Verifikasi Admin",
            [
                "Mahasiswa memahami fitur back-end admin.",
                "Mahasiswa mampu menjelaskan proses approve, reject, dan complete laporan.",
            ],
            [
                "Admin dashboard menampilkan total laporan, laporan pending, laporan approved, laporan completed, total klaim, klaim pending, dan total user mahasiswa. Data ini membantu admin memantau aktivitas Lost and Found.",
                "Pada modul laporan, admin dapat menyetujui laporan, menolak laporan dengan catatan admin, menandai laporan selesai, atau menghapus laporan.",
            ],
            [
                "Login sebagai admin yang dibuat oleh AdminSeeder.",
                "Buka /admin/dashboard.",
                "Buka menu laporan dan lakukan approve pada laporan pending.",
            ],
            """public function approve($id)
{
    $report = Report::findOrFail($id);
    $report->update(['status' => 'approved']);

    return redirect()->back()
        ->with('success', 'Laporan berhasil di-approve.');
}""",
            [
                "Buat laporan sebagai mahasiswa lalu approve sebagai admin.",
                "Amati perbedaan tampilan sebelum dan sesudah approved.",
            ],
        ),
        (
            "Minggu Ke-12  Verifikasi Klaim dan Penyelesaian Laporan",
            [
                "Mahasiswa memahami proses admin menyetujui klaim.",
                "Mahasiswa mampu menjelaskan efek approve klaim terhadap report.",
            ],
            [
                "Ketika admin menyetujui klaim, status klaim berubah menjadi approved dan laporan otomatis menjadi completed. Sistem juga menolak klaim pending lain pada laporan yang sama agar satu barang tidak memiliki lebih dari satu klaim aktif yang disetujui.",
                "Alur ini penting untuk menjaga integritas data pengembalian barang.",
            ],
            [
                "Buka Admin/ClaimController.php.",
                "Perhatikan method approve().",
                "Ajukan lebih dari satu klaim untuk laporan yang sama, lalu approve salah satu klaim.",
            ],
            """$claim->update(['status_klaim' => 'approved']);
$claim->report->update(['status' => 'completed']);

Claim::where('id_report', $claim->id_report)
    ->where('id', '!=', $claim->id)
    ->where('status_klaim', 'pending')
    ->update(['status_klaim' => 'rejected']);""",
            [
                "Jelaskan kenapa laporan menjadi completed saat klaim approved.",
                "Tambahkan skenario pengujian untuk klaim pending lain.",
            ],
        ),
        (
            "Minggu Ke-13  Chatbot Assistant FindIt",
            [
                "Mahasiswa memahami integrasi chatbot pada layout.",
                "Mahasiswa mampu membaca fallback response lokal.",
                "Mahasiswa memahami penggunaan API eksternal secara opsional.",
            ],
            [
                "FindIt memiliki widget chatbot yang tampil untuk user login. Chatbot mengirim pesan ke route chatbot.ask. Controller menyusun konteks data terkini dari database, lalu menggunakan Gemini API jika API key tersedia. Jika API tidak tersedia atau error, sistem memberikan jawaban fallback lokal.",
                "Fallback lokal menangani pertanyaan seperti barang hilang terbaru, barang temuan terbaru, cara lapor, cara klaim, statistik, dan kategori.",
            ],
            [
                "Buka app/Http/Controllers/ChatbotController.php.",
                "Buka resources/views/layouts/app.blade.php dan cari script sendChat().",
                "Uji pertanyaan: cara lapor barang hilang dan lihat barang temuan terbaru.",
            ],
            """fetch('{{ route(\"chatbot.ask\") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
    },
    body: JSON.stringify({ message: msg })
})""",
            [
                "Tambahkan satu pola fallback baru, misalnya pertanyaan tentang kontak admin.",
                "Jelaskan peran CSRF token pada request chatbot.",
            ],
        ),
        (
            "Minggu Ke-14  Pengujian, Dokumentasi, dan Presentasi Project",
            [
                "Mahasiswa mampu menyusun skenario pengujian aplikasi.",
                "Mahasiswa mampu mempresentasikan fitur FindIt secara sistematis.",
                "Mahasiswa mampu membuat dokumentasi teknis singkat.",
            ],
            [
                "Tahap akhir project berfokus pada validasi fitur dan presentasi. Pengujian dilakukan pada alur guest, mahasiswa, dan admin. Dokumentasi minimal berisi tujuan aplikasi, struktur database, daftar route, fitur utama, dan screenshot hasil aplikasi.",
                "Presentasi sebaiknya memperlihatkan demo end-to-end: mahasiswa membuat laporan, admin approve, mahasiswa lain klaim, admin approve klaim, dan laporan berubah menjadi completed.",
            ],
            [
                "Buat checklist pengujian manual.",
                "Jalankan php artisan test untuk memastikan fitur bawaan Laravel tetap aman.",
                "Siapkan alur demo project dari login sampai laporan selesai.",
            ],
            """Contoh skenario demo:
1. Login sebagai mahasiswa A.
2. Buat laporan barang temuan.
3. Login sebagai admin dan approve laporan.
4. Login sebagai mahasiswa B.
5. Ajukan klaim pada barang temuan.
6. Login sebagai admin dan approve klaim.
7. Pastikan laporan berubah menjadi completed.""",
            [
                "Buat slide presentasi berisi latar belakang, ERD, fitur, demo, dan kesimpulan.",
                "Lampirkan screenshot halaman beranda, laporan, klaim, dan dashboard admin.",
            ],
        ),
    ]

    for w in weeks:
        story.extend(week(*w))

    story.append(PageBreak())
    story.append(p("Lampiran A  Ringkasan File Penting", "week"))
    story.append(
        table(
            [
                ["File", "Fungsi"],
                ["routes/web.php", "Mendefinisikan route public, mahasiswa, admin, dan chatbot."],
                ["app/Models/Report.php", "Model laporan barang hilang atau temuan beserta relasi dan helper."],
                ["app/Models/Claim.php", "Model pengajuan klaim barang temuan."],
                ["app/Http/Controllers/ReportController.php", "Mengelola daftar, detail, pembuatan, edit, update, dan hapus laporan."],
                ["app/Http/Controllers/ClaimController.php", "Mengelola pengajuan klaim dan daftar klaim user."],
                ["app/Http/Controllers/Admin/ReportController.php", "Mengelola verifikasi laporan pada area admin."],
                ["app/Http/Controllers/Admin/ClaimController.php", "Mengelola verifikasi klaim pada area admin."],
                ["resources/views/layouts/app.blade.php", "Layout utama halaman user, termasuk navbar, flash message, dan chatbot widget."],
                ["resources/views/home.blade.php", "Halaman beranda FindIt."],
            ],
            [5.4 * cm, 10 * cm],
        )
    )
    story.append(p("Lampiran B  Checklist Pengujian Manual", "section"))
    story.extend(
        bullets(
            [
                "Guest dapat membuka beranda, barang hilang, barang temuan, dan detail barang approved.",
                "Mahasiswa dapat registrasi dan login.",
                "Mahasiswa dapat membuat laporan hilang dan temuan.",
                "Laporan baru muncul sebagai pending dan belum tampil ke publik.",
                "Admin dapat approve laporan pending.",
                "Laporan approved tampil pada halaman publik.",
                "Mahasiswa lain dapat mengajukan klaim pada barang temuan approved.",
                "Mahasiswa tidak dapat klaim laporan miliknya sendiri.",
                "Admin dapat approve klaim dan laporan berubah menjadi completed.",
                "Chatbot menjawab pertanyaan dasar tentang FindIt.",
            ]
        )
    )
    return story


def main():
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    doc = ModuleDocTemplate(str(OUTPUT))
    doc.multiBuild(build_story())
    print(OUTPUT)


if __name__ == "__main__":
    main()
