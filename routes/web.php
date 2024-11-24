<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\AsbensiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// USER
Route::middleware(['guest:pegawai'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

// ADMIN
Route::middleware(['guest:user'])->group(function () {
    Route::get('/admin', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

// Route USER
Route::middleware(['auth:pegawai'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    // absensi
    Route::get('/absensi/create', [AbsensiController::class, 'create']);
    Route::post('absensi/store', [AbsensiController::class, 'store']);

    // Edit profile
    Route::get('/editprofile', [AbsensiController::class, 'editprofile']);
    Route::post('/absensi/{nik}/updateprofile', [AbsensiController::class, 'updateprofile']);

    // Histori 
    Route::get('/absensi/histori', [AbsensiController::class, 'histori']);
    Route::post('/gethistori', [AbsensiController::class, 'gethistori']);

    // Lokasi 
    Route::get('/absensi/lokasi', [AbsensiController::class, 'lokasi']);

    // Izin 
    Route::get('/absensi/izin', [AbsensiController::class, 'izin']);
    Route::get('/absensi/buatizin', [AbsensiController::class, 'buatizin']);
    Route::post('/absensi/storeizin', [AbsensiController::class, 'storeizin']);
    Route::post('/absensi/cekpengajuanizin', [AbsensiController::class, 'cekpengajuanizin']);
    Route::get('/absensi/{id}/edit', [AbsensiController::class, 'editizin']);
    Route::post('/absensi/{id}/update', [AbsensiController::class, 'updateizin']);
    Route::post('/absensi/{id}/deleteizin', [AbsensiController::class, 'deleteizin']);

    // END USER
});

// Route Admin
Route::middleware(['auth:user'])->group(function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/admin/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    // pegawai
    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::post('/pegawai/store', [PegawaiController::class, 'store']);
    Route::post('/pegawai/edit', [PegawaiController::class, 'edit']);
    Route::post('/pegawai/{nik}/update', [PegawaiController::class, 'update']);
    Route::post('/pegawai/{nik}/delete', [PegawaiController::class, 'delete']);

    // Departement
    Route::get('/departemen', [DepartemenController::class, 'index']);
    Route::post('/departemen/store', [DepartemenController::class, 'store']);
    Route::post('/departemen/edit', [DepartemenController::class, 'edit']);
    Route::post('/departemen/{kode_dept}/update', [DepartemenController::class, 'update']);
    Route::post('/departemen/{kode_dept}/delete', [DepartemenController::class, 'delete']);

    // absensi
    Route::get('/absensi/monitoring', [AbsensiController::class, 'monitoring']);
    Route::post('/getabsensi', [AbsensiController::class, 'getabsensi']);
    Route::post('/tampilkanpeta', [AbsensiController::class, 'tampilkanpeta']);
    Route::get('/absensi/laporan', [AbsensiController::class, 'laporan']);
    Route::post('/absensi/cetaklaporan', [AbsensiController::class, 'cetaklaporan']);
    Route::get('/absensi/rekap', [AbsensiController::class, 'rekap']);
    Route::post('/absensi/cetakrekap', [AbsensiController::class, 'cetakrekap']);
    Route::get('/absensi/izinsakit', [AbsensiController::class, 'izinsakit']);
    Route::post('/absensi/approveizinsakit', [AbsensiController::class, 'approveizinsakit']);
    Route::get('/absensi/{id}/batalkanizinsakit', [AbsensiController::class, 'batalkanizinsakit']);


    // Konfigurasi lokasi kantor
    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor']);
});
