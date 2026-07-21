<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\ClassSubjectTeacher;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar materi untuk guru yang sedang login (atau semua jika admin).
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && strtolower($user->role) === 'admin') {
            $materials = Material::with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject')->get();
        } else {
            $materials = Material::whereHas('classSubjectTeacher.teacher.user', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->with('classSubjectTeacher.schoolClass', 'classSubjectTeacher.subject')->get();
        }
        
        return response()->json($materials);
    }

    /**
     * Menyimpan materi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:20480',
        ]);

        $user = Auth::user();
        $teacher = $user ? $user->teacher : null;
        $teacherId = $teacher ? $teacher->id : (\App\Models\Teacher::first()?->id ?? 1);
        
        $cst = ClassSubjectTeacher::firstOrCreate([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
        ]);

        $path = $request->file('file')->store('materials', 'public');

        $material = Material::create([
            'class_subject_teacher_id' => $cst->id,
            'title' => $request->title,
            'file_url' => Storage::url($path),
            'file_type' => $request->file('file')->extension(),
        ]);

        return response()->json(['message' => 'Materi berhasil diunggah.', 'material' => $material], 201);
    }

    /**
     * Menghapus materi dari database.
     */
    public function destroy(Material $material)
    {
        // Pastikan guru yang menghapus adalah pemilik materi
        if (Auth::id() !== $material->classSubjectTeacher->teacher->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus file dari storage
        Storage::disk('public')->delete(str_replace('/storage/', '', $material->file_url));
        
        // Hapus record dari database
        $material->delete();

        return response()->json(['message' => 'Materi berhasil dihapus.']);
    }

    /**
     * Mengambil daftar kelas dan mata pelajaran untuk dropdown formulir.
     */
    public function getFormData()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $cst = $teacher->classSubjectTeachers->unique('class_id')->unique('subject_id')->load('class', 'subject');
        
        return response()->json($cst);
    }
}