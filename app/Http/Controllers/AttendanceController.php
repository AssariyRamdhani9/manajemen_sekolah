<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Student;
use App\Models\ClassSubjectTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Mengambil data siswa dan status absensi berdasarkan class_id dan date.
     */
    public function index(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
        ]);

        $classId = $request->class_id;
        $date = $request->date;

        $students = Student::where('class_id', $classId)
            ->with('user')
            ->get();

        // Ambil absensi yang sudah tersimpan untuk tanggal dan kelas tersebut
        $existingAttendances = Attendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function ($student) use ($existingAttendances) {
            $att = $existingAttendances->get($student->id);
            return [
                'student_id' => $student->id,
                'name' => $student->user ? $student->user->full_name : 'Siswa',
                'nis' => $student->nis,
                'status' => $att ? $att->status : 'Hadir', // default Hadir
                'notes' => $att ? $att->notes : '',
            ];
        });

        return response()->json([
            'date' => $date,
            'students' => $result,
        ]);
    }

    /**
     * Menyimpan atau memperbarui absensi (batch).
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:Hadir,Sakit,Izin,Alfa',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $date = $request->date;
        $classId = $request->class_id;

        // Cari atau buat dummy CST untuk kelas tersebut
        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $classId,
            'subject_id' => \App\Models\Subject::first()?->id ?? 1,
            'teacher_id' => \App\Models\Teacher::first()?->id ?? 1,
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->attendances as $item) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'date' => $date,
                    ],
                    [
                        'class_subject_teacher_id' => $cst->id,
                        'status' => $item['status'],
                        'notes' => $item['notes'] ?? null,
                    ]
                );
            }

            DB::commit();

            return response()->json(['message' => 'Data absensi berhasil disimpan.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan absensi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Memproses presensi berbasis pemindaian QR Code (NIS).
     */
    public function scanQr(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'date' => 'nullable|date',
        ]);

        $nis = trim($request->nis);
        $date = $request->date ?? now()->toDateString();

        $student = Student::where('nis', $nis)->with('user', 'class')->first();

        if (!$student) {
            return response()->json(['message' => "Siswa dengan NIS '{$nis}' tidak ditemukan!"], 404);
        }

        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $student->class_id ?? 1,
            'subject_id' => \App\Models\Subject::first()?->id ?? 1,
            'teacher_id' => \App\Models\Teacher::first()?->id ?? 1,
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $date,
            ],
            [
                'class_subject_teacher_id' => $cst->id,
                'status' => 'Hadir',
                'notes' => 'Presensi via Scanner QR Code (' . now()->format('H:i:s') . ')',
            ]
        );

        return response()->json([
            'message' => 'Presensi QR Code Berhasil!',
            'student' => [
                'id' => $student->id,
                'name' => $student->user ? $student->user->full_name : 'Siswa',
                'nis' => $student->nis,
                'class_name' => $student->class ? $student->class->name : '-',
            ],
            'status' => 'Hadir',
            'time' => now()->format('H:i:s'),
            'date' => $date,
        ]);
    }
}
