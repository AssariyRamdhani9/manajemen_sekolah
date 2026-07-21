<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassSubjectTeacher;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Menampilkan daftar tugas untuk guru yang sedang login (atau semua jika admin).
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && strtolower($user->role) === 'admin') {
            $assignments = Assignment::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject')->get();
        } else {
            $assignments = Assignment::whereHas('classSubjectTeacher.teacher.user', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject')->get();
        }
        
        return response()->json($assignments);
    }

    /**
     * Menyimpan tugas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:20480', // File opsional
        ]);

        $user = Auth::user();
        $teacher = $user ? $user->teacher : null;
        $teacherId = $teacher ? $teacher->id : (\App\Models\Teacher::first()?->id ?? 1);
        
        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('assignments', 'public');
            $filePath = Storage::url($path);
        }

        $assignment = Assignment::create([
            'class_subject_teacher_id' => $cst->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'file_url' => $filePath,
        ]);

        return response()->json(['message' => 'Tugas berhasil diberikan.', 'assignment' => $assignment], 201);
    }

    /**
     * Menghapus tugas dari database.
     */
    public function destroy(Assignment $assignment)
    {
        if (Auth::id() !== $assignment->classSubjectTeacher->teacher->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($assignment->file_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $assignment->file_url));
        }

        $assignment->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus.']);
    }
}