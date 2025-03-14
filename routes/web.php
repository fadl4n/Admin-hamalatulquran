<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController;
use App\Http\Controllers\CMS\DashboardController;
use App\Http\Controllers\CMS\Configuration\GroupMenuController;
use App\Http\Controllers\CMS\Configuration\MenuController;
use App\Http\Controllers\CMS\User\UserController;
use App\Http\Controllers\CMS\User\SantriController;
use App\Http\Controllers\CMS\User\KelasController;
use App\Http\Controllers\CMS\User\ArtikelController;
use App\Http\Controllers\CMS\User\PengajarController;
use App\Http\Controllers\CMS\User\KeluargaController;
use App\Http\Controllers\CMS\User\SetoranController;
use App\Http\Controllers\CMS\User\NilaiController;
use App\Http\Controllers\CMS\User\TargetController;
use App\Http\Controllers\CMS\User\HistoriController;
use App\Http\Controllers\CMS\User\SuratController;
use App\Http\Controllers\CMS\User\RoleController;
use App\Http\Controllers\CMS\Master\RoleController as RoleMaster;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\CheckPriviledge;

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

Route::get('/check', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'index');
    Route::post('login', 'doLogin');
});

// start checking auth
Route::middleware([CheckAuth::class])->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logout');

        Route::get('profile', 'profile');
        Route::post('profile', 'profileUpdate');
    });

    Route::controller(DashboardController::class)->group(function () {
        // Halaman utama dashboard
        Route::get('/', 'index');

        // Rute API untuk mendapatkan statistik
        Route::get('/statistics', 'dashboardStatistics');
    });

    Route::prefix('group-menu')->group(function () {
        Route::controller(GroupMenuController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('create', 'create');
            Route::post('store', 'store');
            Route::get('edit/{id}', 'edit');
            Route::post('update/{id}', 'update');
            Route::get('delete/{id}', 'delete');
            Route::get('fn-get-data', 'fnGetData');
        });
    });

    Route::prefix('menu')->group(function () {
        Route::controller(MenuController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('create', 'create');
            Route::post('store', 'store');
            Route::get('edit/{id}', 'edit');
            Route::post('update/{id}', 'update');
            Route::get('delete/{id}', 'delete');
            Route::get('fn-get-data', 'fnGetData');
        });
    });

    Route::prefix('master')->group(function () {
        Route::controller(RoleMaster::class)->group(function () {
            Route::get('role', 'index');
        });
    });

    // start checking priviledge
    // Route::middleware([CheckPriviledge::class])->group(function () {
    //     Route::prefix('users')->group(function () {
    //         Route::controller(UserController::class)->group(function () {
    //             Route::get('/', 'index');
    //             Route::get('create', 'create');
    //             Route::post('store', 'store');
    //             Route::get('detail/{id}', 'detail');
    //             Route::get('edit/{id}', 'edit');
    //             Route::post('update/{id}', 'update');
    //             Route::get('delete/{id}', 'delete');
    //             Route::get('fn-get-data', 'fnGetData');
    //         });
    //     });


    // });
    // end checking priviledge
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('create', 'create');
            Route::post('store', 'store');
            Route::get('edit/{id}', 'edit');
            Route::post('update/{id}', 'update');
            Route::get('delete/{id}', 'delete');
            Route::get('fn-get-data', 'fnGetData');
        });
    });
    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('create', 'create');
            Route::post('store', 'store');
            Route::get('detail/{id}', 'detail');
            Route::get('edit/{id}', 'edit');
            Route::post('update/{id}', 'update');
            Route::get('delete/{id}', 'delete');
            Route::get('fn-get-data', 'fnGetData');
        });
    });
    Route::prefix('santri')->name('santri.')->group(function () {
        Route::controller(SantriController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('show/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
            Route::get('edit-orangtua/{id}', 'editOrangTua')->name('edit.orangtua');
            Route::put('update-orangtua/{id}', 'updateOrangTua')->name('update.orangtua'); // Pastikan ini ada
            Route::get('edit-wali/{id}', 'editWali')->name('edit.wali');
            Route::put('update-wali/{id}', 'updateWali')->name('update.wali');
        });
    });



        Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::controller(KelasController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update'); // Perbaiki route update
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
        });
    });
    Route::prefix('pengajar')->name('pengajar.')->group(function () {
        Route::controller(PengajarController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::get('show/{id}', 'show')->name('show');
            Route::put('update/{id}', 'update')->name('update'); // Pastikan PUT
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
        });
    });
    Route::prefix('keluarga')->name('keluarga.')->group(function () {
        Route::controller(KeluargaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{keluarga}', 'edit')->name('edit'); // Gunakan {keluarga} untuk Route Model Binding
            Route::put('update/{keluarga}', 'update')->name('update');
            Route::delete('delete/{keluarga}', 'destroy')->name('destroy');
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
        });
    });
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::controller(SuratController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{surat}', 'edit')->name('edit'); // Menggunakan {surat} untuk binding
            Route::put('update/{surat}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy'); // Tetap menggunakan {id}
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
        });
    });

    Route::prefix('setoran')->name('setoran.')->group(function () {
        Route::controller(SetoranController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{setoran}', 'edit')->name('edit');
            Route::put('update/{setoran}', 'update')->name('update');
            Route::delete('delete-target/{id_santri}/{idGroup}', 'destroyByTarget')->name('destroyByTarget'); // Gunakan id_santri dan idGroup
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
            Route::get('get-santri-targets/{id_santri}', 'getSantriTargets')->name('getSantriTargets');
            Route::get('get-nama-surat', 'getNamaSuratByGroup')->name('getNamaSuratByGroup');
            Route::delete('destroy/{idSetoran}', 'destroy')->name('destroy');
            Route::get('detail/{groupKey}', 'show')->name('show');
        });
    });


    Route::prefix('nilai')->name('nilai.')->group(function () {
        Route::get('/', [NilaiController::class, 'index'])->name('index');
        Route::get('/{idSantri}/{idGroup}', [NilaiController::class, 'show'])->name('show');

        // Route untuk mendapatkan data menggunakan AJAX
        Route::get('/fn-get-data', [NilaiController::class, 'fnGetData'])->name('fn-get-data');
    });



    Route::prefix('target')->name('target.')->group(function () {
        Route::controller(TargetController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'create')->name('create.post'); // Tambahkan POST untuk 'create'
            Route::post('store', 'store')->name('store');
            Route::get('edit/{target}', 'edit')->name('edit');
            Route::put('update/{target}', 'update')->name('update');
            Route::delete('delete/{id_santri}/{id_group}', 'destroy')->name('destroy');
            Route::delete('delete/{id_target}', 'destroyByIdTarget')->name('destroyByIdTarget'); // Route untuk hapus berdasarkan id_target
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
            Route::get('detail/{id_group}', 'detail')->name('detail');
        });
    });

    Route::prefix('artikel')->group(function () {
        Route::get('/', [ArtikelController::class, 'index'])->name('artikel.index');
        Route::get('/create', [ArtikelController::class, 'create'])->name('artikel.create');
        Route::post('/store', [ArtikelController::class, 'store'])->name('artikel.store');
        Route::get('/edit/{id}', [ArtikelController::class, 'edit'])->name('artikel.edit');
        Route::put('/update/{id}', [ArtikelController::class, 'update'])->name('artikel.update');
        Route::delete('/delete/{id}', [ArtikelController::class, 'destroy'])->name('artikel.destroy');
        Route::get('/fn-get-data', [ArtikelController::class, 'fnGetData']);
    });
    Route::prefix('histori')->name('histori.')->group(function () {
        Route::controller(HistoriController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('fn-get-data', 'fnGetData')->name('fnGetData');
            Route::post('update-nilai/{id_target}', 'updateNilai')->name('updateNilai'); // Update berdasarkan id_target
            Route::post('update-histori/{id_target}', 'updateHistori')->name('updateHistori'); // Tambahkan ini
        });
    });
    Route::get('/histori/get-preview/{id}', [HistoriController::class, 'getPreview'])->name('histori.getPreview');



    // web.php
Route::get('/setoran/targets/{santri_id}', [SetoranController::class, 'getTargetsBySantri'])->name('setoran.getTargetsBySantri');
Route::get('/get-nama-surat', [SetoranController::class, 'getNamaSurat']);
Route::get('get-target-detail-by-surat', [TargetController::class, 'getTargetDetailBySurat']);
Route::get('get-ayats-validation', [SetoranController::class, 'validateAyat']);
Route::get('/get-id-target', [SetoranController::class, 'getIdTarget'])->name('setoran.getIdTarget');




});
// end checking auth
