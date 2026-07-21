<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

// Redirect root ke /login
Route::redirect('/', '/login');

// --- Halaman Auth (Login & Register) ---
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');

// --- Grup Protected Route ---
Route::middleware(['auth'])->group(function () {

    // Dashboard sesuai role
    Route::get('/dashboard/admin', function () {
        return Inertia::render('Admin/AdminDashboard', [
            'stats' => [
                'students' => \App\Models\Student::count(),
                'teachers' => \App\Models\Teacher::count(),
                'activeClasses' => \App\Models\Classes::count(),
                'submittedAssignments' => \App\Models\Submission::count(),
            ]
        ]);
    })->middleware('role:admin')->name('dashboard.admin');

    Route::get('/dashboard/teacher', function () {
        return Inertia::render('Guru/TeacherDashboard');
    })->middleware('role:guru,teacher')->name('dashboard.teacher');

    Route::get('/dashboard/siswa', function () {
        $user = auth()->user();
        $student = $user ? \App\Models\Student::where('user_id', $user->id)->with('class')->first() : null;

        return Inertia::render('Siswa/StudentDashboard', [
            'student' => $student,
            'user' => $user,
        ]);
    })->middleware('role:siswa')->name('dashboard.siswa');

    // Admin pages
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/students', fn() => Inertia::render('Admin/ManageStudents'));
        Route::get('/admin/teachers', fn() => Inertia::render('Admin/ManageTeachers'));
        Route::get('/admin/subjects', fn() => Inertia::render('Admin/ManageSubjects'));
        Route::get('/admin/classes', fn() => Inertia::render('Admin/ManageClasses'));
    });

    // Teacher pages
    Route::middleware('role:guru,teacher')->group(function () {
        Route::get('/teacher/schedules', fn() => Inertia::render('Guru/ManageSchedules'));
        Route::get('/teacher/materials', fn() => Inertia::render('Guru/ManageMaterials'));
        Route::get('/teacher/assignments', fn() => Inertia::render('Guru/ManageAssignments'));
        Route::get('/teacher/attendances', fn() => Inertia::render('Guru/ManageAttendance'));
        Route::get('/teacher/qr-scanner', fn() => Inertia::render('Guru/QRScanner'));
        Route::get('/teacher/grades', fn() => Inertia::render('Guru/ManageGrades'));
        Route::get('/teacher/exams', fn() => Inertia::render('Guru/ManageExams'));
        Route::get('/teacher/exams/{id}/results', fn($id) => Inertia::render('Guru/ExamResults', ['examId' => $id]));
        Route::get('/teacher/announcements', fn() => Inertia::render('Announcements'));
        Route::get('/guru/announcements', fn() => Inertia::render('Announcements'));
    });

    // Student pages
    Route::middleware('role:siswa')->group(function () {
        Route::get('/student/schedules', fn() => Inertia::render('Siswa/StudentSchedule'));
        Route::get('/student/assignments', fn() => Inertia::render('Siswa/MyAssignments'));
        Route::get('/student/report', fn() => Inertia::render('Siswa/StudentReport'));
        Route::get('/student/card', fn() => Inertia::render('Siswa/StudentCard'));
        Route::get('/student/exams', fn() => Inertia::render('Siswa/StudentExams'));
        Route::get('/student/exams/{id}/take', fn($id) => Inertia::render('Siswa/TakeExam', ['examId' => $id]));
        Route::get('/student/announcements', fn() => Inertia::render('Announcements'));
        Route::get('/siswa/announcements', fn() => Inertia::render('Announcements'));
    });

    // Admin & Print pages
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/announcements', fn() => Inertia::render('Announcements'));
    });

    Route::get('/report/print/{student_id?}', fn($student_id = null) => Inertia::render('ReportPrint', ['studentId' => $student_id]));
});
