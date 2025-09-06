<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangStokController;
use App\Http\Controllers\MstBarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Exports\BarangStokExport;
use App\Http\Controllers\LogPencatatanController;
use App\Http\Controllers\DashboardController;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request; // tambahkan ini di atas


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/signin', [AuthController::class, 'login'])->name('signin');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('dashboard.index');
    });

    Route::get('/stock-barang', [BarangStokController::class, 'inputStock'])->name('barangstok.input_stock');
    Route::get('/stock-barang/data', [BarangStokController::class, 'getData'])->name('barangstok.data');
    Route::post('/stock-barang/store', [BarangStokController::class, 'store'])->name('barangstok.store');
    Route::get('/stock-barang/{barang}/history', [BarangStokController::class, 'historyPage'])->whereNumber('barang')->name('barangstok.historyPage');
    Route::get   ('/stock-barang/history/{id}/edit',  [BarangStokController::class,'historyEdit'])->name('barangstok.history.edit');
    Route::post  ('/stock-barang/history/{id}',       [BarangStokController::class,'historyUpdate'])->name('barangstok.history.update');
    Route::delete('/stock-barang/history/{id}',       [BarangStokController::class,'historyDestroy'])->name('barangstok.history.destroy');

    // DataTables histori (AJAX)
    Route::get('/stock-barang/history', [BarangStokController::class, 'history'])
        ->name('barangstok.history');
    Route::get('/stock-barang/edit/{id}', [BarangStokController::class, 'edit'])->name('barangstok.edit');
    Route::post('/stock-barang/update/{id}', [BarangStokController::class, 'update'])->name('barangstok.update');
    Route::delete('/stock-barang/delete/{id}', [BarangStokController::class, 'destroy'])->name('barangstok.delete');
    Route::post('/rak/store', [BarangStokController::class, 'storeRak'])->name('rak.store');


    Route::get('/logstok-barang', [LogPencatatanController::class, 'inputStock'])->name('logstok.input_stock');
    Route::get('/logstok-barang/data', [LogPencatatanController::class, 'getData'])->name('logstok.data');
    Route::post('/logstok-barang/store', [LogPencatatanController::class, 'store'])->name('logstok.store');
    Route::get('/logstok-barang/edit/{id}', [LogPencatatanController::class, 'edit'])->name('logstok.edit');
    Route::post('/logstok-barang/update/{id}', [LogPencatatanController::class, 'update'])->name('logstok.update');
    Route::delete('/logstok-barang/delete/{id}', [LogPencatatanController::class, 'destroy'])->name('logstok.delete');
    Route::get('/logstok-barang/{id}', [LogPencatatanController::class, 'showBarang'])->name('logstok.show');



    Route::get('/stock-barang/export', function (Request $request) {
        $barangId = $request->get('barang_id');
        return Excel::download(new BarangStokExport($barangId), 'stok-barang.xlsx');
    })->name('barangstok.export');

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
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/', [MemberController::class,'index'])->name('index');
        Route::get('/data', [MemberController::class,'getData'])->name('data');
        Route::post('/store', [MemberController::class,'store'])->name('store');
        Route::post('/update/{id}', [MemberController::class,'update'])->name('update');
        Route::get('/edit/{id}', [MemberController::class,'edit'])->name('edit');
        Route::delete('/delete/{id}', [MemberController::class,'destroy'])->name('delete');
    });

        Route::get('/rak/{rak}/shafts', [BarangStokController::class, 'getShafts'])->name('rak.shafts');
        Route::get('/stock-barang/barang/{id}', [BarangStokController::class, 'showBarang'])->name('barangstok.showBarang');
        Route::prefix('mst')->name('mst.')->group(function () {
            Route::delete('/barang/{id}/hapus', [BarangStokController::class, 'hapus'])
                ->name('barang.hapus');   
        });
        Route::get('/rak/list-rak-barang', [BarangStokController::class, 'listRakBarang'])
         ->name('rak.list');
        // REPORT STOCK
        Route::get('/stock-barang/report', [BarangStokController::class, 'reportPage'])
            ->name('barangstok.reportPage');
        Route::get('/stock-barang/report-data', [BarangStokController::class, 'reportData'])
            ->name('barangstok.reportData');

            // Dashboard page
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->name('dashboard');

        // AJAX DataTables
        Route::get('/dashboard/expired', [DashboardController::class, 'dashboardExpired'])
            ->name('dashboard.expired');
        Route::get('/dashboard/fast-moving', [DashboardController::class, 'dashboardFast'])
            ->name('dashboard.fast');

});
