<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PengajarController;
use App\Http\Controllers\API\SantriController;
use App\Http\Controllers\API\KelasController;
use App\Http\Middleware\CheckAuthFrontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengajar;
use App\Models\Santri;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('/', function (Request $request) {
    return 'Laravel ' . app()->version();
});

Route::get('/health-check', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'OK',
    ], 200);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'doLogin');
});

// start checking auth
Route::middleware([])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('profile/{role}/{identifier}', 'profile');
        Route::put('profile', 'profileUpdate');
        Route::post('profile/change-password', 'changePassword');
    });
});

// Route untuk get semua santri & santri berdasarkan ID
Route::controller(SantriController::class)->group(function () {
    Route::get('/santri', 'getAllSantri');        // Get semua santri
    Route::get('/santri/{id}', 'getSantriById');  // Get santri by ID
    Route::get('/santri/by-kelas/{id}', 'getSantriByKelas');  // Get santri by ID
});

// Route untuk get semua santri & santri berdasarkan ID
Route::controller(PengajarController::class)->group(function () {
    Route::get('/pengajar', 'getAllPengajar');    // Get semua pengajar
    Route::get('/pengajar/{id}', 'getPengajarById'); // Get pengajar by ID
});

Route::controller(KelasController::class)->group(function () {
    Route::get('/kelas', 'getAllKelas');    // Get semua kelas
    Route::get('/kelas/{id}', 'getKelasById'); // Get kelas by ID
});


// end checking auth
