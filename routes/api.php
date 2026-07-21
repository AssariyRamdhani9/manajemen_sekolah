<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AnnouncementController;


// --- Rute Autentikasi ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// --- Grup Rute yang Dilindungi ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::post('/announcements', [AnnouncementController::class, 'store'])->middleware('role:admin,teacher,guru');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->middleware('role:admin,teacher,guru');
    
    Route::get('/attendances', [\App\Http\Controllers\AttendanceController::class, 'index']);
    Route::post('/attendances', [\App\Http\Controllers\AttendanceController::class, 'store']);
    
    // PERBAIKAN KRUSIAL DI SINI: Memuat relasi teacher
    // Ini memastikan frontend mendapatkan teacher.id = 1 (yang benar)
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['teacher', 'student']);
    });

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
    Route::middleware('role:guru,teacher')->prefix('guru')->group(function () {
        Route::get('schedules/data', [ScheduleController::class, 'getFormData']);
        Route::apiResource('schedules', ScheduleController::class);
        Route::get('classes', [ClassController::class, 'index']);
        Route::get('subjects', [SubjectController::class, 'index']);
        // Rute Manajemen Materi Pembelajaran
        Route::apiResource('materials', MaterialController::class)->except(['show', 'update']);
        Route::get('materials/data', [MaterialController::class, 'getFormData']);
        Route::apiResource('assignments', AssignmentController::class)->except(['show', 'update']);
        
    });

    // =========================
    // Rute untuk Siswa
    // =========================
    Route::middleware('role:siswa')->prefix('siswa')->group(function () { 
        // Ini rute yang benar untuk siswa
        Route::get('schedules', [ScheduleController::class, 'studentSchedule']);
        Route::post('submissions', [SubmissionController::class, 'store']);
        Route::get('assignments', [SubmissionController::class, 'index']);
        
        // Tambahin rute tugas/materi di sini
        // Route::get('tasks', [TaskController::class, 'index']);
    });
});
