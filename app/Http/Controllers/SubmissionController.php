<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Menampilkan daftar tugas untuk siswa yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user ? $user->student : null;
        
        if (!$student) {
            if ($user && strtolower($user->role) === 'admin') {
                return response()->json(Assignment::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject', 'submissions')->get());
            }
            return response()->json([]);
        }

        $assignments = Assignment::whereHas('classSubjectTeacher.schoolClass', function ($query) use ($student) {
            $query->where('classes.id', $student->class_id);
        })->with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject')
          ->with(['submissions' => function ($query) use ($student) {
              $query->where('student_id', $student->id);
          }])
          ->get();
        
        return response()->json($assignments);
    }

    /**
     * Mengunggah file tugas dan menyimpannya sebagai pengumpulan tugas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file|max:20480', // Batas 20MB
        ]);

        $user = Auth::user();
        $student = $user ? $user->student : null;

        if (!$student) {
            return response()->json(['message' => 'Hanya akun siswa yang dapat mengumpulkan tugas.'], 403);
        }

        // Cek apakah siswa sudah mengumpulkan tugas ini sebelumnya
        $existingSubmission = Submission::where('assignment_id', $request->assignment_id)
                                        ->where('student_id', $student->id)
                                        ->first();
        
        if ($existingSubmission) {
            // Jika sudah ada, hapus file lama
            Storage::disk('public')->delete(str_replace('/storage/', '', $existingSubmission->file_url));
            $existingSubmission->delete();
        }

        $path = $request->file('file')->store('submissions', 'public');

        $submission = Submission::create([
            'assignment_id' => $request->assignment_id,
            'student_id' => $student->id,
            'file_url' => Storage::url($path),
            'submitted_at' => now(),
        ]);

        return response()->json(['message' => 'Tugas berhasil dikumpulkan.', 'submission' => $submission], 201);
    }
}