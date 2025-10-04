<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleController;

// --- Rute Autentikasi ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// --- Grup Rute yang Dilindungi ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    // =========================
    // Rute untuk Admin
    // =========================
        Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::apiResource('students', StudentController::class);
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('classes', ClassController::class);
    });


    // =========================
    // Rute untuk Guru
    // =========================
    Route::middleware('role:guru')->prefix('guru')->group(function () {
        Route::apiResource('schedules', ScheduleController::class);
        Route::get('schedules/data', [ScheduleController::class, 'getFormData']);
        Route::get('classes', [ClassController::class, 'index']);
        Route::get('subjects', [SubjectController::class, 'index']);
    });

    // =========================
    // Rute untuk Siswa
    // =========================
    Route::middleware('role:siswa')->prefix('siswa')->group(function () {
        Route::get('schedules', [ScheduleController::class, 'index']);
        // Tambahin rute tugas/materi di sini
        // Route::get('tasks', [TaskController::class, 'index']);
    });
});
