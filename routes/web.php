<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (bisa diakses semua orang)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index']);

// Test Email (remove saat production)
Route::get('/test-email', function () {
    $claim = \App\Models\Claim::first();
    if (!$claim) {
        return response()->json(['error' => 'No claim found'], 404);
    }
    $claim->user->notify(new \App\Notifications\ClaimStatusNotification($claim, $claim->report, 'approved'));
    return response()->json(['sent' => true, 'email' => $claim->user->email]);
});

// Test Report Email (remove saat production)
Route::get('/test-report-email', function (\Illuminate\Http\Request $request) {
    // Ambil report terbaru atau spesifik via ?report_id=
    $report = $request->report_id
        ? \App\Models\Report::find($request->report_id)
        : \App\Models\Report::latest()->first();

    if (!$report) {
        return response()->json(['error' => 'No report found'], 404);
    }

    // Kirim notifikasi
    $report->user->notify(new \App\Notifications\ReportSubmittedNotification($report, $report->jenis_laporan));

    return response()->json([
        'sent' => true,
        'email' => $report->user->email,
        'jenis' => $report->jenis_laporan,
        'report_id' => $report->id,
        'user_name' => $report->user->name,
    ]);
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Barang hilang & temuan (public)
Route::get('/barang-hilang', [ReportController::class, 'hilang'])->name('reports.hilang');
Route::get('/barang-temuan', [ReportController::class, 'temuan'])->name('reports.temuan');
Route::get('/barang/{id}', [ReportController::class, 'show'])->name('reports.show');

// Chatbot (public)
Route::post('/chatbot', [App\Http\Controllers\ChatbotController::class, 'ask'])
    ->name('chatbot.ask');

/*
|--------------------------------------------------------------------------
| AUTH USER ROUTES (khusus mahasiswa yang sudah login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard user
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Laporan
    Route::get('/laporan/buat', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/laporan/buat', [ReportController::class, 'store'])->middleware('throttle.reports')->name('reports.store');
    Route::get('/laporan/saya', [ReportController::class, 'myReports'])->name('my.reports');
    Route::get('/laporan/{id}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/laporan/{id}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/laporan/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // ⚠️ KLAIM — /saya harus SEBELUM /{reportId}
    Route::get('/klaim/saya', [ClaimController::class, 'myClaims'])->name('my.claims');
    Route::get('/klaim/{reportId}/ajukan', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/klaim/{reportId}/ajukan', [ClaimController::class, 'store'])->middleware('throttle.claims')->name('claims.store');
    Route::delete('/klaim/{id}', [ClaimController::class, 'cancel'])->name('claims.cancel');

    // Notifications API
    Route::get('/notifications/api', [NotificationController::class, 'apiIndex']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Testimoni
    Route::get('/testimoni/buat/{claimId}', [App\Http\Controllers\TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/testimoni', [App\Http\Controllers\TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('/testimoni/saya', [App\Http\Controllers\TestimonialController::class, 'myTestimonials'])->name('my.testimonials');
    Route::get('/testimoni/{id}/edit', [App\Http\Controllers\TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('/testimoni/{id}', [App\Http\Controllers\TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/testimoni/{id}', [App\Http\Controllers\TestimonialController::class, 'destroy'])->name('testimonials.destroy');
});
/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (khusus admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Laporan
    Route::get('/laporan', [App\Http\Controllers\Admin\ReportController::class, 'index'])
        ->name('reports.index');
    Route::get('/laporan/{id}', [App\Http\Controllers\Admin\ReportController::class, 'show'])
        ->name('reports.show');
    Route::patch('/laporan/{id}/approve', [App\Http\Controllers\Admin\ReportController::class, 'approve'])
        ->name('reports.approve');
    Route::patch('/laporan/{id}/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject'])
        ->name('reports.reject');
    Route::patch('/laporan/{id}/complete', [App\Http\Controllers\Admin\ReportController::class, 'complete'])
        ->name('reports.complete');
    Route::delete('/laporan/{id}', [App\Http\Controllers\Admin\ReportController::class, 'destroy'])
        ->name('reports.destroy');

    // Klaim
    Route::get('/klaim', [App\Http\Controllers\Admin\ClaimController::class, 'index'])
        ->name('claims.index');
    Route::get('/klaim/{id}', [App\Http\Controllers\Admin\ClaimController::class, 'show'])
        ->name('claims.show');
    Route::patch('/klaim/{id}/approve', [App\Http\Controllers\Admin\ClaimController::class, 'approve'])
        ->name('claims.approve');
    Route::patch('/klaim/{id}/reject', [App\Http\Controllers\Admin\ClaimController::class, 'reject'])
        ->name('claims.reject');

    // User
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])
        ->name('users.index');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])
        ->name('users.destroy');

    // Kategori
    Route::get('/kategori', [App\Http\Controllers\Admin\CategoryController::class, 'index'])
        ->name('categories.index');
    Route::post('/kategori', [App\Http\Controllers\Admin\CategoryController::class, 'store'])
        ->name('categories.store');
    Route::get('/kategori/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])
        ->name('categories.edit');
    Route::put('/kategori/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])
        ->name('categories.update');
    Route::delete('/kategori/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    // Testimoni
    Route::get('/testimoni', [App\Http\Controllers\Admin\TestimonialController::class, 'index'])
        ->name('testimonials.index');
    Route::delete('/testimoni/{id}', [App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])
        ->name('testimonials.destroy');

});

// Compro Routes
require __DIR__.'/compro.php';

// Auth routes dari Breeze
require __DIR__.'/auth.php'; 
