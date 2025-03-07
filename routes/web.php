<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PengurusController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisIuranController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\IuranAnggotaController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PendaftaranAnggotaController;
use App\Http\Controllers\PenjualanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::middleware('auth')->group(function(){

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.main');

    Route::prefix('/pengurus')->as('pengurus')->controller(PengurusController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.deelete');
    });

    Route::prefix('/pendaftaran-anggota')->as('pendaftaran_anggota')->controller(PendaftaranAnggotaController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::post('/update-status', 'updateStatus')->name('.updateStatus');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/anggota')->as('anggota')->controller(AnggotaController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::get('/nonaktif', 'nonaktif')->name('.nonaktif');
        Route::post('/nonaktif/store', 'nonaktifStore')->name('.nonaktifStore');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/jenis-iuran')->as('jenis_iuran')->controller(JenisIuranController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/iuran-anggota')->as('iuran_anggota')->controller(IuranAnggotaController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/{anggota:id_anggota}', 'detail')->name('.detail');
        Route::get('/{anggota:id_anggota}/{periode_iuran}', 'payment')->name('.payment');
        Route::get('/{anggota:id_anggota}/{periode_iuran}/print', 'print')->name('.print');
        Route::post('/store', 'store')->name('.store');

        // Route::get('/{anggota:id_anggota}/wajib/', 'wajibPayment')->name('.wajib.payment');
        // Route::get('/{anggota:id_anggota}/wajib/print', 'wajibPrint')->name('.wajib.print');
        // Route::post('/wajib/store', 'wajibStore')->name('.wajib.store');
    });

    Route::prefix('/pengaturan')->as('pengaturan')->controller(PengaturanController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    // POS

    Route::prefix('/kategori-produk')->as('kategori_produk')->controller(KategoriProdukController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/produk')->as('produk')->controller(ProdukController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/search', 'search')->name('.search');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/supplier')->as('supplier')->controller(SupplierController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/pembelian')->as('pembelian')->controller(PembelianController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form', 'form')->name('.form');
        Route::get('/print', 'print')->name('.print');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });

    Route::prefix('/penjualan')->as('penjualan')->controller(PenjualanController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/pos', 'pos')->name('.pos');
        Route::get('/form', 'form')->name('.form');
        Route::get('/print', 'print')->name('.print');
        Route::post('/store', 'store')->name('.store');
        Route::delete('/delete/{id}', 'delete')->name('.delete');
    });
});

