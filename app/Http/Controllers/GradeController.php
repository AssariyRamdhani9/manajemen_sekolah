<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\ClassSubjectTeacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Mengambil daftar nilai siswa untuk kelas dan mata pelajaran tertentu.
     */
    public function index(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $classId = $request->class_id;
        $subjectId = $request->subject_id;
        $semester = $request->semester ?? '1';
        $academicYear = $request->academic_year ?? '2025/2026';

        $user = Auth::user();
        $teacher = $user ? $user->teacher : null;
        $teacherId = $teacher ? $teacher->id : (\App\Models\Teacher::first()?->id ?? 1);

        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $classId,
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
        ]);

        $students = Student::where('class_id', $classId)->with('user')->get();

        $existingGrades = Grade::where('class_subject_teacher_id', $cst->id)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function ($student) use ($existingGrades) {
            $g = $existingGrades->get($student->id);
            return [
                'student_id' => $student->id,
                'name' => $student->user ? $student->user->full_name : 'Siswa',
                'nis' => $student->nis,
                'assignment_score' => $g ? (float)$g->assignment_score : 0,
                'uts_score' => $g ? (float)$g->uts_score : 0,
                'uas_score' => $g ? (float)$g->uas_score : 0,
                'final_score' => $g ? (float)$g->final_score : 0,
                'letter_grade' => $g ? $g->letter_grade : 'D',
                'teacher_notes' => $g ? $g->teacher_notes : '',
            ];
        });

        return response()->json([
            'cst_id' => $cst->id,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'grades' => $result,
        ]);
    }

    /**
     * Menyimpan atau memperbarui batch nilai siswa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.assignment_score' => 'numeric|min:0|max:100',
            'grades.*.uts_score' => 'numeric|min:0|max:100',
            'grades.*.uas_score' => 'numeric|min:0|max:100',
            'grades.*.teacher_notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $teacher = $user ? $user->teacher : null;
        $teacherId = $teacher ? $teacher->id : (\App\Models\Teacher::first()?->id ?? 1);

        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->grades as $item) {
                $assignment = (float)($item['assignment_score'] ?? 0);
                $uts = (float)($item['uts_score'] ?? 0);
                $uas = (float)($item['uas_score'] ?? 0);

                // Perhitungan Bobot: 30% Tugas + 30% UTS + 40% UAS
                $finalScore = round(($assignment * 0.30) + ($uts * 0.30) + ($uas * 0.40), 2);

                $letterGrade = 'D';
                if ($finalScore >= 85) {
                    $letterGrade = 'A';
                } elseif ($finalScore >= 75) {
                    $letterGrade = 'B';
                } elseif ($finalScore >= 65) {
                    $letterGrade = 'C';
                }

                Grade::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'class_subject_teacher_id' => $cst->id,
                        'semester' => $request->semester,
                        'academic_year' => $request->academic_year,
                    ],
                    [
                        'assignment_score' => $assignment,
                        'uts_score' => $uts,
                        'uas_score' => $uas,
                        'final_score' => $finalScore,
                        'letter_grade' => $letterGrade,
                        'teacher_notes' => $item['teacher_notes'] ?? null,
                    ]
                );
            }

            DB::commit();

            return response()->json(['message' => 'Data nilai berhasil disimpan.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan data nilai.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mendapatkan rekap nilai Rapor Digital untuk siswa.
     */
    public function studentReport(Request $request)
    {
        $user = Auth::user();
        $student = null;

        if ($request->has('student_id')) {
            $student = Student::with('user', 'class')->find($request->student_id);
        } else {
            $student = $user ? $user->student : null;
            if ($student) {
                $student->load('user', 'class');
            }
        }

        if (!$student) {
            // Fallback untuk admin jika membuka tanpa ID
            $student = Student::with('user', 'class')->first();
        }

        if (!$student) {
            return response()->json(['message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $semester = $request->semester ?? '1';
        $academicYear = $request->academic_year ?? '2025/2026';

        // Ambil nilai seluruh mata pelajaran
        $grades = Grade::where('student_id', $student->id)
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->with('classSubjectTeacher.subject', 'classSubjectTeacher.teacher.user')
            ->get();

        // Ambil ringkasan presensi
        $attendances = Attendance::where('student_id', $student->id)->get();
        $attendanceSummary = [
            'hadir' => $attendances->where('status', 'Hadir')->count(),
            'sakit' => $attendances->where('status', 'Sakit')->count(),
            'izin' => $attendances->where('status', 'Izin')->count(),
            'alfa' => $attendances->where('status', 'Alfa')->count(),
        ];

        $averageScore = $grades->count() > 0 ? round($grades->avg('final_score'), 2) : 0;

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->user ? $student->user->full_name : 'Siswa',
                'nis' => $student->nis,
                'class_name' => $student->class ? $student->class->name : '-',
            ],
            'semester' => $semester,
            'academic_year' => $academicYear,
            'average_score' => $averageScore,
            'attendance' => $attendanceSummary,
            'grades' => $grades->map(function ($g) {
                return [
                    'id' => $g->id,
                    'subject_name' => $g->classSubjectTeacher ? ($g->classSubjectTeacher->subject ? $g->classSubjectTeacher->subject->name : '-') : '-',
                    'teacher_name' => $g->classSubjectTeacher && $g->classSubjectTeacher->teacher && $g->classSubjectTeacher->teacher->user ? $g->classSubjectTeacher->teacher->user->full_name : '-',
                    'assignment_score' => (float)$g->assignment_score,
                    'uts_score' => (float)$g->uts_score,
                    'uas_score' => (float)$g->uas_score,
                    'final_score' => (float)$g->final_score,
                    'letter_grade' => $g->letter_grade,
                    'teacher_notes' => $g->teacher_notes ?? 'Sangat baik dalam mengikuti pembelajaran.',
                ];
            }),
        ]);
    }
}
