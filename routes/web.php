<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\KonfigurasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Guest (Belum Login)
|--------------------------------------------------------------------------
*/

// Guest Perangkat Desa
Route::middleware(['guest:perangkat'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

// Guest Admin Panel
Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');

    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

/*
|--------------------------------------------------------------------------
| Web Routes - Perangkat Desa (Terproteksi)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:perangkat'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    // Presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);

    // Edit Profile
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('/presensi/{nik}/updateprofile', [PresensiController::class, 'updateprofile']);

    // Histori Presensi
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

    // Pengajuan Izin / Sakit Perangkat
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
});

/*
|--------------------------------------------------------------------------
| Web Routes - Admin Panel (Terproteksi)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:user'])->group(function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin'])->name('dashboardadmin');

    // Manajemen Data Perangkat Desa
    Route::get('/panel/perangkat', [PerangkatController::class, 'index']);
    Route::post('/panel/perangkat/store', [PerangkatController::class, 'store']);
    Route::post('/panel/perangkat/edit', [PerangkatController::class, 'edit'])->name('perangkat.edit');
    Route::post('/panel/perangkat/{nik}/update', [PerangkatController::class, 'update']);
    Route::post('/panel/perangkat/{nik}/delete', [PerangkatController::class, 'delete']);

    // Monitoring Presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);

    // Persetujuan (Approval) Izin / Sakit
    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit'])->name('izinsakit');
    Route::post('/presensi/approveizinsakit', [PresensiController::class, 'approveizinsakit'])->name('approveizinsakit');
    Route::get('/presensi/{id}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit'])->name('batalkanizinsakit');
    
    // Laporan & Rekap
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);

    // Konfigurasi Lokasi & Admin
    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor']);
    Route::post('/konfigurasi/storeadmin', [KonfigurasiController::class, 'storeadmin']);
    Route::post('/konfigurasi/editadmin', [KonfigurasiController::class, 'editadmin']);
    Route::post('/konfigurasi/updateadmin/{id}', [KonfigurasiController::class, 'updateadmin']);
    Route::post('/konfigurasi/deleteadmin/{id}', [KonfigurasiController::class, 'deleteadmin']);
});