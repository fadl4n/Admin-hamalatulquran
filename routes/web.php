<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController;
use App\Http\Controllers\CMS\DashboardController;
use App\Http\Controllers\CMS\Configuration\GroupMenuController;
use App\Http\Controllers\CMS\Configuration\MenuController;
use App\Http\Controllers\CMS\User\UserController;
use App\Http\Controllers\CMS\User\SantriController;
use App\Http\Controllers\CMS\User\KelasController;
use App\Http\Controllers\CMS\User\RoleController;
use App\Http\Controllers\CMS\Master\RoleController as RoleMaster;
use App\Http\Controllers\SantriController as ControllersSantriController;
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
        Route::get('/', 'index');
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
    Route::prefix('santri')->group(function () {
        Route::controller(SantriController::class)->group(function () {
            Route::get('/', 'index'); // Menampilkan daftar santri
            Route::get('create', 'create'); // Form tambah santri
            Route::post('store', 'store'); // Menyimpan data santri
            Route::get('edit/{id}', 'edit'); // Form edit santri
            Route::post('update/{id}', 'update'); // Memperbarui data santri
            Route::delete('delete/{id}', [SantriController::class, 'destroy']);
            Route::get('fn-get-data', 'fnGetData'); // DataTables API
        });
    });
    Route::prefix('kelas')->group(function () {
        Route::controller(KelasController::class)->group(function () {
            Route::get('/', 'index'); // Menampilkan daftar santri
            Route::get('create', 'create'); // Form tambah santri
            Route::post('store', 'store'); // Menyimpan data santri
            Route::get('edit/{id}', 'edit'); // Form edit santri
            Route::post('update/{id}', 'update'); // Memperbarui data santri
            Route::delete('delete/{id}', [KelasController::class, 'destroy']);
            Route::get('fn-get-data', 'fnGetData'); // DataTables API
        });
    });

});
// end checking auth
