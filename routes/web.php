<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

// --- Halaman Auth (Login & Register) ---
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');

// --- Grup Protected Route ---
Route::middleware(['auth'])->group(function () {

    // Dashboard sesuai role
    Route::get('/dashboard/admin', function () {
        return Inertia::render('Admin/AdminDashboard');
    })->middleware('role:admin')->name('dashboard.admin');

    Route::get('/dashboard/teacher', function () {
        return Inertia::render('Guru/TeacherDashboard');
    })->middleware('role:guru')->name('dashboard.teacher');

    Route::get('/dashboard/siswa', function () {
        return Inertia::render('Siswa/StudentDashboard');
    })->middleware('role:siswa')->name('dashboard.siswa');

    // Admin pages
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/students', fn() => Inertia::render('Admin/ManageStudents'));
        Route::get('/admin/teachers', fn() => Inertia::render('Admin/ManageTeachers'));
        Route::get('/admin/subjects', fn() => Inertia::render('Admin/ManageSubjects'));
        Route::get('/admin/classes', fn() => Inertia::render('Admin/ManageClasses'));
    });

    // Teacher pages
    Route::middleware('role:guru')->group(function () {
        Route::get('/teacher/schedules', fn() => Inertia::render('Guru/ManageSchedules'));
    });
});
