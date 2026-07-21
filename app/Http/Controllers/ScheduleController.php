<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Schedule::with([
            'classSubjectTeacher.schoolClass', 
            'classSubjectTeacher.subject',
            'classSubjectTeacher.teacher'
        ]);

        // Jika yang mengakses adalah Guru (bukan Admin), filter berdasarkan ID guru tersebut
        if ($user && strtolower($user->role) !== 'admin') {
            if ($user->teacher) {
                $teacherId = $user->teacher->id;
                $query->whereHas('classSubjectTeacher', function ($q) use ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                });
            } else {
                return response()->json([]);
            }
        }

        $schedules = $query->orderBy('start_time')->get();

        return response()->json($schedules);
    }

    /**
     * Menyimpan jadwal baru (tanpa guru).
     */
    public function store(Request $request)
    {
        // 1. Tambahkan validasi untuk teacher_id
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id', // WAJIB ada dan valid
            'day_of_week'=> 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat', // Mengurangi hari Minggu/Sabtu jika tidak dipakai
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string',
        ]);

        // 2. Cari atau buat penugasan ClassSubjectTeacher secara otomatis jika belum ada
        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
        ]);

        // 3. Buat jadwal
        $schedule = Schedule::create([
            'class_subject_teacher_id' => $cst->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'room'       => $request->room,
        ]);

        return response()->json([
            'message'  => 'Jadwal berhasil ditambahkan.',
            'schedule' => $schedule->load('classSubjectTeacher'),
        ], 201);
    }

    /**
     * Memperbarui jadwal yang ada.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id', 
            'day_of_week'=> 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string',
        ]);

        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
        ]);

        $schedule->update([
            'class_subject_teacher_id' => $cst->id,
            'day_of_week' => $request->day_of_week,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'room'        => $request->room,
        ]);

        return response()->json([
            'message'  => 'Jadwal berhasil diperbarui.',
            'schedule' => $schedule->load('classSubjectTeacher'),
        ]);
    }

    /**
     * Menghapus jadwal.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }

    /**
     * Data untuk form input.
     */
    public function getFormData()
    {
        $classes  = Classes::all(['id', 'name']);
        $subjects = Subject::all(['id', 'name']);

        return response()->json([
            'classes'  => $classes,
            'subjects' => $subjects,
        ]);
    }

     public function studentSchedule(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'siswa') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Cari ID siswa dari user yang login
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Cari jadwal berdasarkan class_id siswa
        $schedules = Schedule::whereHas('classSubjectTeacher', function ($query) use ($student) {
            $query->where('class_id', $student->class_id);
        })
        ->with('classSubjectTeacher.subject', 'classSubjectTeacher.teacher.user')
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->get();

        return response()->json($schedules);
    }
}
