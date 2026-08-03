<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\BleController;
use App\Http\Controllers\EventController;
use App\Models\Role;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
Route::middleware('auth:api', 'role:peserta')->group(function () {
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::get('/presensi/riwayat', [PresensiController::class, 'riwayatPresensi']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api', 'role:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/ble', [BleController::class, 'store']);
    Route::get('/ble', [BleController::class, 'index']);
    Route::put('/ble/{id}', [BleController::class, 'update']);
    Route::delete('/ble/{id}', [BleController::class, 'destroy']);
    Route::post('/sesi', [EventController::class, 'store']);
    Route::get('/sesi', [EventController::class, 'index']);
    Route::put('/sesi/{id}', [EventController::class, 'update']);
    Route::delete('/sesi/{id}', [EventController::class, 'destroy']);
});
