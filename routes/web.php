<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AttendanceRecapController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\MeetingFormBuilderController;
use App\Http\Controllers\Admin\MeetingParticipantController;
use App\Http\Controllers\Admin\MeetingQrController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ParticipantLoginController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\PuzzleCaptchaController;
use App\Http\Controllers\SecurityChallengeController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — DIHADIR
|--------------------------------------------------------------------------
| Seluruh URL menggunakan format Clean URLs (tanpa ekstensi .php).
| Otorisasi dilindungi di level server menggunakan Gates & Role Middleware.
*/

// Jalur Verifikasi Keamanan Interstitial (Cloudflare-style Challenge)
Route::get('/cdn-cgi/challenge', [SecurityChallengeController::class, 'show'])->name('security.challenge');
Route::post('/cdn-cgi/verify', [SecurityChallengeController::class, 'verify'])->name('security.verify');

// Halaman Publik (Landing Page, Legalitas, Robots & Sitemap)
Route::get('/', function () {
    return view('landing.index');
})->name('landing');

Route::get('/kebijakan-privasi', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/syarat-ketentuan', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::get('/robots.txt', function () {
    $path = public_path('robots.txt');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
    return response("User-agent: *\nAllow: /\nSitemap: https://dihadir.wacanateknologi.id/sitemap.xml\n", 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->name('robots');

Route::get('/llms.txt', function () {
    $path = public_path('llms.txt');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
    abort(404);
})->name('llms');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Avatar Routes (DiceBear Dynamic & User Avatar)
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/avatar/user/{user}', [AvatarController::class, 'showUser'])->name('avatar.user');
    // Pratinjau avatar tetap publik (dipakai juga pada formulir login), namun
    // jumlah berkas cache dibatasi di AvatarService agar tidak membanjiri disk.
    Route::get('/avatar/preview', [AvatarController::class, 'preview'])->name('avatar.preview');
});

// Self-Hosted Puzzle Captcha Routes (Adopsi Mandiri dari SIPBJ)
Route::prefix('api/puzzle')->middleware('throttle:60,1')->name('puzzle.')->group(function () {
    Route::get('/challenge', [PuzzleCaptchaController::class, 'challenge'])->name('challenge');
    Route::post('/check', [PuzzleCaptchaController::class, 'check'])->name('check');
});

// Autentikasi Pengelola (Admin & Super Admin)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:admin-login')->name('login.submit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Area Panel Admin yang Dilindungi
    Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
        // 1. Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // 2. Data Pertemuan (Fase 2)
        Route::post('/pertemuan/{meeting}/duplicate', [MeetingController::class, 'duplicate'])->name('pertemuan.duplicate');
        Route::post('/pertemuan/{meeting}/toggle-form', [MeetingController::class, 'toggleFormStatus'])->name('pertemuan.toggle-form');
        Route::get('/pertemuan/{meeting}/peserta', [MeetingParticipantController::class, 'index'])->name('pertemuan.peserta.index');
        Route::post('/pertemuan/{meeting}/peserta', [MeetingParticipantController::class, 'store'])->name('pertemuan.peserta.store');
        Route::post('/pertemuan/{meeting}/peserta/sync-all', [MeetingParticipantController::class, 'syncAll'])->name('pertemuan.peserta.sync-all');
        Route::delete('/pertemuan/{meeting}/peserta/{participant}', [MeetingParticipantController::class, 'destroy'])->name('pertemuan.peserta.destroy');
        Route::get('/pertemuan/{meeting}/form-builder', [MeetingFormBuilderController::class, 'index'])->name('pertemuan.builder.index');
        Route::post('/pertemuan/{meeting}/form-builder', [MeetingFormBuilderController::class, 'storeField'])->name('pertemuan.builder.store');
        Route::post('/pertemuan/{meeting}/form-builder/template', [MeetingFormBuilderController::class, 'applyTemplate'])->name('pertemuan.builder.template');
        Route::post('/pertemuan/{meeting}/form-builder/{field}/toggle', [MeetingFormBuilderController::class, 'toggleActive'])->name('pertemuan.builder.toggle');
        Route::put('/pertemuan/{meeting}/form-builder/{field}', [MeetingFormBuilderController::class, 'updateField'])->name('pertemuan.builder.update');
        Route::delete('/pertemuan/{meeting}/form-builder/{field}', [MeetingFormBuilderController::class, 'destroyField'])->name('pertemuan.builder.destroy');
        Route::post('/pertemuan/{meeting}/form-builder/{field}/reorder/{direction}', [MeetingFormBuilderController::class, 'reorder'])->name('pertemuan.builder.reorder');
        Route::get('/pertemuan/{meeting}/qr', [MeetingQrController::class, 'show'])->name('pertemuan.qr');
        Route::get('/pertemuan/{meeting}/qr/unduh', [MeetingQrController::class, 'download'])->name('pertemuan.qr.download');
        Route::get('/pertemuan/{meeting}/qr/cetak', [MeetingQrController::class, 'print'])->name('pertemuan.qr.print');
        Route::resource('/pertemuan', MeetingController::class)->parameters(['pertemuan' => 'meeting']);

        // 3. Data Peserta (Fase 3)
        Route::get('/peserta/template-csv', [ParticipantController::class, 'downloadTemplateCsv'])->name('peserta.template-csv');
        Route::post('/peserta/import', [ParticipantController::class, 'import'])->name('peserta.import');
        Route::resource('/peserta', ParticipantController::class)->parameters(['peserta' => 'participant']);

        // 4. Rekap Kehadiran (Fase 5)
        Route::get('/rekap', [AttendanceRecapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/tanda-tangan/{attendance}', [AttendanceRecapController::class, 'showSignature'])->name('rekap.signature');
        Route::get('/rekap/lampiran/{answer}', [AttendanceRecapController::class, 'showAttachment'])->name('rekap.attachment');
        Route::post('/rekap/{attendance}/status', [AttendanceRecapController::class, 'updateStatus'])->name('rekap.status');

        // 5. Laporan (Fase 5)
        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/{meeting}/pdf', [ReportController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/{meeting}/csv', [ReportController::class, 'exportCsv'])->name('laporan.csv');
        Route::get('/laporan/{meeting}/cetak', [ReportController::class, 'printView'])->name('laporan.print');

        // 6. Pengaturan (Fase 6)
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan/profil', [SettingController::class, 'updateProfile'])->name('pengaturan.profile');
        Route::put('/pengaturan/sistem', [SettingController::class, 'updateSystem'])->name('pengaturan.system');

        // 7. Kelola Akun Admin & Staf (Khusus Super Admin)
        Route::middleware('role:super_admin')->group(function () {
            Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
            // Tidak ada halaman detail terpisah: daftar akun sudah memuat seluruh
            // informasi, sehingga rute show tidak didaftarkan agar tidak 500.
            Route::resource('/users', AdminUserController::class)->except(['show']);
        });

        // 8. Audit Log (khusus Super Admin, hanya baca)
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
        });
    });
});

// Form Login Absensi Peserta (Fase 3)
Route::prefix('absensi')->name('absensi.')->group(function () {
    Route::get('/login', [ParticipantLoginController::class, 'showLoginForm'])->name('login');
    Route::get('/{token}/login', [ParticipantLoginController::class, 'showLoginForm'])
        ->where('token', '[A-Za-z0-9]{16,64}')
        ->name('meeting.login');
    Route::post('/login', [ParticipantLoginController::class, 'login'])->middleware('throttle:participant-login')->name('login.submit');
    Route::post('/logout', [ParticipantLoginController::class, 'logout'])->name('logout');
});

// Tautan Formulir Kehadiran Publik (Fase 04)
// Token dibatasi formatnya agar pemindaian URL acak tidak membebani database.
Route::prefix('p')->group(function () {
    Route::get('/{token}', [PublicAttendanceController::class, 'show'])
        ->middleware('throttle:public-form')
        ->where('token', '[A-Za-z0-9]{16,64}')
        ->name('absensi.form');
    Route::post('/{token}', [PublicAttendanceController::class, 'submit'])
        ->middleware('throttle:attendance-submit')
        ->where('token', '[A-Za-z0-9]{16,64}')
        ->name('absensi.submit');
    Route::get('/{token}/selesai', [PublicAttendanceController::class, 'success'])
        ->where('token', '[A-Za-z0-9]{16,64}')
        ->name('absensi.success');
});
