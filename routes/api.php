<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\BleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Models\Role;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
Route::middleware('auth:api', 'role:peserta')->group(function () {
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::get('/presensi/riwayat', [PresensiController::class, 'riwayatPresensi']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/face/register', [UserController::class, 'updateFace']);
});

Route::middleware('auth:api', 'role:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/ble', [BleController::class, 'store']);
    Route::get('/ble', [BleController::class, 'index']);
    Route::put('/ble/{id}', [BleController::class, 'update']);
    Route::delete('/ble/{id}', [BleController::class, 'destroy']);
    Route::post('/event', [EventController::class, 'store']);
    Route::get('/event', [EventController::class, 'index']);
    Route::put('/event/{id}', [EventController::class, 'update']);
    Route::delete('/event/{id}', [EventController::class, 'destroy']);
    Route::post('/peserta', [UserController::class, 'store']);
    Route::get('/peserta', [UserController::class, 'index']);
    Route::put('/peserta/{id}', [UserController::class, 'update']);
    Route::delete('/peserta/{id}', [UserController::class, 'destroy']);
});
