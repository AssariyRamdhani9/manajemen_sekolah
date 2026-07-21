<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\ClassSubjectTeacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Mengambil daftar ujian untuk Guru/Admin atau Siswa.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user ? $user->role : 'siswa');

        if ($role === 'siswa' || $role === 'student') {
            $student = $user->student;
            if (!$student) {
                return response()->json([]);
            }

            $exams = Exam::whereHas('classSubjectTeacher', function ($query) use ($student) {
                $query->where('class_id', $student->class_id);
            })
            ->where('is_published', true)
            ->with(['classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass', 'questions'])
            ->with(['attempts' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json($exams->map(function ($exam) {
                $attempt = $exam->attempts->first();
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'exam_type' => $exam->exam_type ?? 'Kuis',
                    'description' => $exam->description,
                    'duration_minutes' => $exam->duration_minutes,
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                    'subject_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->subject ? $exam->classSubjectTeacher->subject->name : '-') : '-',
                    'class_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->schoolClass ? $exam->classSubjectTeacher->schoolClass->name : '-') : '-',
                    'total_questions' => $exam->questions->count(),
                    'attempt' => $attempt ? [
                        'status' => $attempt->status,
                        'score' => (float)$attempt->score,
                        'started_at' => $attempt->started_at,
                        'finished_at' => $attempt->finished_at,
                    ] : null,
                ];
            }));
        }

        // Untuk Guru / Admin
        $exams = Exam::with(['classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass', 'questions', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($exams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'exam_type' => $exam->exam_type ?? 'Kuis',
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'start_time' => $exam->start_time,
                'end_time' => $exam->end_time,
                'is_published' => (bool)$exam->is_published,
                'subject_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->subject ? $exam->classSubjectTeacher->subject->name : '-') : '-',
                'class_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->schoolClass ? $exam->classSubjectTeacher->schoolClass->name : '-') : '-',
                'class_id' => $exam->classSubjectTeacher ? $exam->classSubjectTeacher->class_id : null,
                'subject_id' => $exam->classSubjectTeacher ? $exam->classSubjectTeacher->subject_id : null,
                'total_questions' => $exam->questions->count(),
                'total_attempts' => $exam->attempts->count(),
            ];
        }));
    }

    /**
     * Menyimpan paket ujian baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'exam_type' => 'required|in:Kuis,UTS,UAS',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $teacher = $user ? $user->teacher : null;
        $teacherId = $teacher ? $teacher->id : (\App\Models\Teacher::first()?->id ?? 1);

        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
        ]);

        $exam = Exam::create([
            'class_subject_teacher_id' => $cst->id,
            'title' => $request->title,
            'exam_type' => $request->exam_type,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'is_published' => true,
        ]);

        return response()->json(['message' => 'Ujian berhasil dibuat.', 'exam' => $exam], 201);
    }

    /**
     * Menampilkan detail ujian dan soal-soalnya.
     */
    public function show($id)
    {
        $user = Auth::user();
        $role = strtolower($user ? $user->role : 'siswa');
        $exam = Exam::with(['classSubjectTeacher.subject', 'classSubjectTeacher.schoolClass', 'questions'])->findOrFail($id);

        // Jika siswa, sembunyikan jawaban benar dari payload
        if ($role === 'siswa' || $role === 'student') {
            $questions = $exam->questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'option_a' => $q->option_a,
                    'option_b' => $q->option_b,
                    'option_c' => $q->option_c,
                    'option_d' => $q->option_d,
                ];
            });

            $student = $user->student;
            $attempt = $student ? ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->first() : null;

            return response()->json([
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'subject_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->subject ? $exam->classSubjectTeacher->subject->name : '-') : '-',
                'questions' => $questions,
                'attempt' => $attempt,
            ]);
        }

        return response()->json($exam);
    }

    /**
     * Menghapus ujian.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return response()->json(['message' => 'Ujian berhasil dihapus.']);
    }

    /**
     * Menambahkan soal baru ke paket ujian.
     */
    public function addQuestion(Request $request, Exam $exam)
    {
        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $question = $exam->questions()->create([
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_option' => strtoupper($request->correct_option),
            'score_weight' => 1,
        ]);

        return response()->json(['message' => 'Soal berhasil ditambahkan.', 'question' => $question], 201);
    }

    /**
     * Menghapus soal dari ujian.
     */
    public function deleteQuestion(ExamQuestion $question)
    {
        $question->delete();
        return response()->json(['message' => 'Soal berhasil dihapus.']);
    }

    /**
     * Memulai sesi pengerjaan ujian oleh siswa.
     */
    public function startAttempt(Exam $exam)
    {
        $user = Auth::user();
        $student = $user ? $user->student : null;

        if (!$student) {
            return response()->json(['message' => 'Hanya akun siswa yang dapat mengerjakan ujian.'], 403);
        }

        $attempt = ExamAttempt::firstOrCreate(
            [
                'exam_id' => $exam->id,
                'student_id' => $student->id,
            ],
            [
                'started_at' => now(),
                'status' => 'in_progress',
                'answers' => [],
                'score' => 0,
            ]
        );

        return response()->json(['message' => 'Sesi ujian dimulai.', 'attempt' => $attempt]);
    }

    /**
     * Mengumpulkan jawaban ujian siswa & menghitung nilai otomatis.
     */
    public function submitAttempt(Request $request, Exam $exam)
    {
        $user = Auth::user();
        $student = $user ? $user->student : null;

        if (!$student) {
            return response()->json(['message' => 'Hanya akun siswa yang dapat mengumpulkan ujian.'], 403);
        }

        $attempt = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->firstOrFail();

        $userAnswers = $request->input('answers', []);
        $questions = $exam->questions;

        $totalQuestions = $questions->count();
        $correctCount = 0;

        foreach ($questions as $q) {
            $questionId = (string)$q->id;
            if (isset($userAnswers[$questionId]) && strtoupper($userAnswers[$questionId]) === strtoupper($q->correct_option)) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

        $attempt->update([
            'answers' => $userAnswers,
            'score' => $score,
            'finished_at' => now(),
            'status' => 'completed',
        ]);

        // Otomatis sinkronkan nilai ke Modul Rapor jika tipe ujian adalah UTS atau UAS
        $type = strtoupper($exam->exam_type ?? 'KUIS');
        if (in_array($type, ['UTS', 'UAS'])) {
            $grade = \App\Models\Grade::firstOrNew([
                'student_id' => $student->id,
                'class_subject_teacher_id' => $exam->class_subject_teacher_id,
                'semester' => '1',
                'academic_year' => '2025/2026',
            ]);

            if ($type === 'UTS') {
                $grade->uts_score = $score;
            } elseif ($type === 'UAS') {
                $grade->uas_score = $score;
            }

            $ass = (float)$grade->assignment_score;
            $uts = (float)$grade->uts_score;
            $uas = (float)$grade->uas_score;
            $finalScore = round(($ass * 0.30) + ($uts * 0.30) + ($uas * 0.40), 2);

            $letterGrade = 'D';
            if ($finalScore >= 85) $letterGrade = 'A';
            elseif ($finalScore >= 75) $letterGrade = 'B';
            elseif ($finalScore >= 65) $letterGrade = 'C';

            $grade->final_score = $finalScore;
            $grade->letter_grade = $letterGrade;
            $grade->save();
        }

        return response()->json([
            'message' => 'Ujian berhasil dikumpulkan.',
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
        ]);
    }

    /**
     * Menampilkan rekap hasil nilai ujian siswa bagi Guru/Admin.
     */
    public function results(Exam $exam)
    {
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with('student.user')
            ->get();

        return response()->json([
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'subject_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->subject ? $exam->classSubjectTeacher->subject->name : '-') : '-',
                'class_name' => $exam->classSubjectTeacher ? ($exam->classSubjectTeacher->schoolClass ? $exam->classSubjectTeacher->schoolClass->name : '-') : '-',
                'total_questions' => $exam->questions->count(),
            ],
            'results' => $attempts->map(function ($att) {
                return [
                    'id' => $att->id,
                    'student_id' => $att->student_id,
                    'student_name' => $att->student && $att->student->user ? $att->student->user->full_name : 'Siswa',
                    'nis' => $att->student ? $att->student->nis : '-',
                    'score' => (float)$att->score,
                    'status' => $att->status,
                    'started_at' => $att->started_at,
                    'finished_at' => $att->finished_at,
                ];
            }),
        ]);
    }
}
