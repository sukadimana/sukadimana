<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Mahasiswa\Index as MahasiswaIndex;
use App\Livewire\MasterBiaya\Index as MasterBiayaIndex;
use App\Livewire\Prodi\Index as ProdiIndex;
use App\Livewire\TahunAkademik\Index as TahunAkademikIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    Route::middleware('role:admin,akademik')->group(function () {
        Route::get('/mahasiswa', MahasiswaIndex::class)->name('mahasiswa.index');
        Route::get('/prodi', ProdiIndex::class)->name('prodi.index');
        Route::get('/tahun-akademik', TahunAkademikIndex::class)->name('tahun-akademik.index');
        Route::get('/master-biaya', MasterBiayaIndex::class)->name('master-biaya.index');
    });
});
