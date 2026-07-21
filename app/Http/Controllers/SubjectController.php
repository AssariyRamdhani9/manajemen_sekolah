<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Menampilkan daftar mata pelajaran (atau mapel yang diampu guru tertentu).
     */
    public function index(Request $request)
    {
        $teacherId = $request->query('teacher_id');

        if ($teacherId) {
            $subjects = Subject::whereHas('classSubjectTeachers', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->get();

            // Jika guru belum ada penugasan khusus, fallback ke semua mapel agar form tetap bisa diisi
            if ($subjects->isEmpty()) {
                $subjects = Subject::all();
            }
        } else {
            $subjects = Subject::all();
        }

        return response()->json($subjects);
    }

    /**
     * Menyimpan mata pelajaran baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:subjects,name',
        ]);

        $subject = Subject::create($request->all());

        return response()->json([
            'message' => 'Mata pelajaran berhasil ditambahkan.',
            'subject' => $subject
        ], 201);
    }

    /**
     * Memperbarui data mata pelajaran yang ada.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('subjects', 'name')->ignore($subject->id)],
        ]);

        $subject->update($request->all());

        return response()->json([
            'message' => 'Mata pelajaran berhasil diperbarui.',
            'subject' => $subject
        ]);
    }

    /**
     * Menghapus mata pelajaran dari database.
     */
    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return response()->json(['message' => 'Mata pelajaran berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus mata pelajaran. Pastikan tidak ada guru yang mengajar mata pelajaran ini.'], 500);
        }
    }
}