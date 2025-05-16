<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PengajarController;
use App\Http\Controllers\API\SantriController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\SuratController;
use App\Http\Controllers\API\TargetController;
use App\Http\Controllers\API\SetoranController;
use App\Http\Controllers\API\HistoriController;
use App\Http\Controllers\API\NilaiController;
use App\Http\Controllers\API\ArtikelController;
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
    Route::get('/santri', 'getAllSantri'); // Get semua santri
    Route::get('/santri/jumlah-aktif', 'countAktif');
    Route::get('/santri/{id}', 'getSantriById');  // Get santri by ID
    Route::get('/santri/by-kelas/{id}', 'getSantriByKelas');  // Get santri by Kelas
    Route::get('/santri/{id}/group', 'getGroupFromTarget'); // Get TargetGroup by IdSantri
});

// Route untuk get semua pengajar & pengajar berdasarkan ID
Route::controller(PengajarController::class)->group(function () {
    Route::get('/pengajar', 'getAllPengajar');    // Get semua pengajar
    Route::get('/pengajar/{id}', 'getPengajarById'); // Get pengajar by ID
});

Route::controller(KelasController::class)->group(function () {
    Route::get('/kelas', 'getAllKelas');    // Get semua kelas
    Route::get('/kelas/{id}', 'getKelasById'); // Get kelas by ID
});

Route::get('/surat', [SuratController::class, 'index']);

Route::middleware([])->group(function () {
    Route::get('/target', [TargetController::class, 'index']);
    Route::get('/target/id/{id_target}', [TargetController::class, 'show']);
    Route::get('/target/santri/{id_santri}', [TargetController::class, 'getAllTargetBySantri']);
    Route::get('/target/{id_santri}/{id_group}', [TargetController::class, 'getBySantriGroup']);
    Route::post('/target', [TargetController::class, 'store']);
    Route::put('/target/{id_target}', [TargetController::class, 'update']);
    Route::delete('/target/{id_santri}/{id_group}', [TargetController::class, 'destroyBySantriGroup']);
    Route::delete('/target/{id_target}', [TargetController::class, 'destroy']);
});

Route::middleware([])->group(function () {
    Route::get('/setoran', [SetoranController::class, 'index']); // GET semua setoran
    Route::get('/setoran/target/{id_santri}', [SetoranController::class, 'gettargetBySantri']);
    Route::get('/setoran/target-santri/{id_santri}-{id_target}', [SetoranController::class, 'getSetoranSantriByTarget']);
    // Route::get('/setoran/{idSantri}/{idGroup}', [SetoranController::class, 'getSetoranBySantriAndGroup']);
    Route::get('/setoran/get-id-target', [SetoranController::class, 'getIdTarget']);
    Route::get('/setoran/nama-surat/{group_id}/{santri_id}', [SetoranController::class, 'getNamaSurat']);
    Route::get('/setoran/validate-ayat', [SetoranController::class, 'validateAyat']);
    Route::get('/setoran/target-detail', [SetoranController::class, 'getTargetDetailBySurat']);
    Route::put('/setoran/{id}', [SetoranController::class, 'update']);
    Route::post('/setoran', [SetoranController::class, 'store']);
    Route::delete('/setoran/{id}', [SetoranController::class, 'destroy']);
    Route::delete('/setoran/{idSantri}/{idGroup}', [SetoranController::class, 'destroyByTarget']);
});

Route::middleware([])->group(function () {
    Route::get('/histori', [HistoriController::class, 'index']);
    Route::get('/histori/santri/{id_santri}', [HistoriController::class, 'showBySantri']);
    Route::post('/histori/update-nilai/{id_target}', [HistoriController::class, 'updateNilai']);
    Route::get('/histori/get-preview/{id_target}', [HistoriController::class, 'getPreview']);
    Route::post('/histori/update/{id}', [HistoriController::class, 'updateHistori']);
});


Route::middleware([])->group(function () {
    Route::get('/nilai', [NilaiController::class, 'index']);
    Route::get('/nilai/{idSantri}/{idGroup}', [NilaiController::class, 'show']);
    Route::get('/nilai/datatable/data', [NilaiController::class, 'fnGetData']);
});

Route::middleware([])->group(function () {
    Route::get('/artikel', [ArtikelController::class, 'index']);
});

// end checking auth
