<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangStokController;
use App\Http\Controllers\MstBarangController;
use App\Http\Controllers\UserController;

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

Route::get('/', function () {
    return view('dashboard/index');
});


Route::get('/stock-barang', [BarangStokController::class, 'inputStock'])->name('barangstok.input_stock');
Route::get('/stock-barang/data', [BarangStokController::class, 'getData'])->name('barangstok.data');
Route::post('/stock-barang/store', [BarangStokController::class, 'store'])->name('barangstok.store');
Route::get('/stock-barang/edit/{id}', [BarangStokController::class, 'edit'])->name('barangstok.edit');
Route::post('/stock-barang/update/{id}', [BarangStokController::class, 'update'])->name('barangstok.update');
Route::delete('/stock-barang/delete/{id}', [BarangStokController::class, 'destroy'])->name('barangstok.delete');

Route::prefix('mst')->name('mst.')->group(function () {
    Route::get('/barang', [MstBarangController::class, 'index'])->name('barang.index');
    Route::get('/barang-ajax', [MstBarangController::class, 'ajax'])->name('barang.ajax');
    Route::post('/barang-simpan', [MstBarangController::class, 'store'])->name('barang.simpan');
    Route::get('/barang/{id}/edit', [MstBarangController::class, 'edit'])->name('barang.edit');
    Route::delete('/barang/{id}', [MstBarangController::class, 'destroy'])->name('barang.destroy');
});

Route::prefix('user')->name('user.')->group(function(){
    Route::get('/', [UserController::class,'index'])->name('index');
    Route::get('/data', [UserController::class,'getData'])->name('data');
    Route::post('/store', [UserController::class,'store'])->name('store');
    Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::get('/edit/{id}', [UserController::class,'edit'])->name('edit');
    Route::delete('/delete/{id}', [UserController::class,'destroy'])->name('delete');
});




