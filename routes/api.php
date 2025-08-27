<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;

// Authentication Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected Routes with Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Student Management
        Route::apiResource('students', AdminController::class);

        // Teacher Management
        Route::apiResource('teachers', AdminController::class);
        
        // Classes and Subjects Management
        Route::apiResource('classes', AdminController::class);
        Route::apiResource('subjects', AdminController::class);
        Route::apiResource('class-subject-teacher', AdminController::class);
        
        // Announcement Management
        Route::apiResource('announcements', AdminController::class);
    });

    // Teacher Routes
    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
        // Schedule Management
        Route::apiResource('schedules', TeacherController::class);

        // Materials Management
        Route::apiResource('materials', TeacherController::class);

        // Assignment Management
        Route::apiResource('assignments', TeacherController::class);

        // Attendance Management
        Route::apiResource('attendances', TeacherController::class);

        // Submission Management
        Route::apiResource('submissions', TeacherController::class);
    });

    // Student Routes
    Route::middleware('role:student')->prefix('student')->group(function () {
        Route::get('schedules', [StudentController::class, 'getSchedules']);
        Route::get('materials', [StudentController::class, 'getMaterials']);
        Route::get('assignments', [StudentController::class, 'getAssignments']);
        Route::post('submissions', [StudentController::class, 'submitAssignment']);
        Route::get('attendances', [StudentController::class, 'getAttendances']);
        Route::get('announcements', [StudentController::class, 'getAnnouncements']);
    });

});
