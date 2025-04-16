<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TargetController;
use App\Http\Controllers\API\SetoranController;
use App\Http\Controllers\API\NilaiController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\ArtikelController;
use App\Http\Controllers\API\SantriController;
use App\Http\Middleware\CheckAuthFrontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HistoriController;
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
Route::middleware(['firebase.auth'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('profile/{role}/{id}', 'profile');
        Route::put('profile', 'profileUpdate');
        Route::post('profile/change-password', 'changePassword');
    });
});

Route::middleware('api')->group(function () {
    Route::get('/target', [TargetController::class, 'index']);
    Route::get('/target/id/{id_target}', [TargetController::class, 'show']);
    Route::get('/target/{id_santri}/{id_group}', [TargetController::class, 'getBySantriGroup']);
    Route::post('/target', [TargetController::class, 'store']);
    Route::put('/target/{id_target}', [TargetController::class, 'update']);
    Route::delete('/target/{id_santri}/{id_group}', [TargetController::class, 'destroyBySantriGroup']);
    Route::delete('/target/{id_target}', [TargetController::class, 'destroy']);
});


Route::middleware('api')->group(function () {
    Route::put('/setoran/{id}', [SetoranController::class, 'update']);
    Route::get('/setoran', [SetoranController::class, 'index']); // GET semua setoran
    Route::get('/setoran/{groupKey}', [SetoranController::class, 'showBySantriFormatted']);
    Route::post('/setoran', [SetoranController::class, 'store']);
    Route::delete('/setoran/{id}', [SetoranController::class, 'destroy']);
    Route::delete('/setoran/{idSantri}/{idGroup}', [SetoranController::class, 'destroyByTarget']);
    Route::get('/setoran/targets/{id_santri}', [SetoranController::class, 'getTargetsBySantri']);
    Route::get('/setoran/nama-surat/{group_id}/{santri_id}', [SetoranController::class, 'getNamaSurat']);
    Route::get('/setoran/validate-ayat', [SetoranController::class, 'validateAyat']);
    Route::get('/setoran/target-detail', [SetoranController::class, 'getTargetDetailBySurat']);
    Route::get('/get-id-target', [SetoranController::class, 'getIdTarget']);

});

Route::middleware('api')->group(function () {
    Route::get('/histori', [HistoriController::class, 'index']);
    Route::post('/histori/update-nilai/{id_target}', [HistoriController::class, 'updateNilai']);
    Route::get('/histori/get-preview/{id_target}', [HistoriController::class, 'getPreview']);
    Route::post('/histori/update/{id}', [HistoriController::class, 'updateHistori']);
});


Route::middleware('api')->group(function () {
    Route::get('/nilai', [NilaiController::class, 'index']);
    Route::get('/nilai/{idSantri}/{idGroup}', [NilaiController::class, 'show']);
    Route::get('/nilai/datatable/data', [NilaiController::class, 'fnGetData']);
});

Route::middleware('api')->group(function () {
    Route::get('/santri', [SantriController::class, 'index']);
    Route::get('/santri/data', [SantriController::class, 'DataSantri']);

});

Route::middleware('api')->group(function () {
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::get('/kelas/santri/{id}', [KelasController::class, 'getSantriByKelas']);
});

Route::middleware('api')->group(function () {
    Route::get('/artikel', [ArtikelController::class, 'index']);

});

// end checking auth
