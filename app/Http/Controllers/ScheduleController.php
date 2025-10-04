<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectTeacher;
use App\Models\Schedule;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal.
     */
    public function index()
    {
        $schedules = Schedule::with(['class', 'subject'])->get();

        return response()->json($schedules);
    }

    /**
     * Menyimpan jadwal baru (tanpa guru).
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week'=> 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string',
        ]);

        // Cari relasi class_subject_teacher
        $cst = ClassSubjectTeacher::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if (!$cst) {
            return response()->json([
                'message' => 'Kombinasi kelas dan mata pelajaran tidak memiliki guru yang terdaftar.'
            ], 422);
        }

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
     * Memperbarui jadwal yang ada (tanpa guru).
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week'=> 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string',
        ]);

        $cst = ClassSubjectTeacher::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if (!$cst) {
            return response()->json([
                'message' => 'Kombinasi kelas dan mata pelajaran tidak memiliki guru yang terdaftar.'
            ], 422);
        }

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
}
