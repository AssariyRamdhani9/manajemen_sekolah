<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Menampilkan daftar guru
     */
    public function index()
    {
        $teachers = Teacher::with(['user', 'subject_taught'])->get();

        return response()->json([
            'message' => 'Daftar guru berhasil diambil.',
            'teachers' => $teachers
        ]);
    }

    /**
     * Menyimpan data guru baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nip' => 'required|string|unique:teachers,nip',
            'subject_taught_id' => 'nullable|exists:subjects,id',
        ]);

        DB::beginTransaction();

        try {
            // Simpan user
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'guru',
            ]);

            // Simpan teacher
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'subject_taught_id' => $request->subject_taught_id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Data guru berhasil disimpan.',
                'teacher' => $teacher->load('user', 'subject_taught')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan data guru.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui data guru
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user->id)],
            'nip' => ['required', 'string', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'subject_taught_id' => 'nullable|exists:subjects,id',
        ]);

        DB::beginTransaction();

        try {
            // Update user
            $teacher->user->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
            ]);

            // Update teacher
            $teacher->update([
                'nip' => $request->nip,
                'subject_taught_id' => $request->subject_taught_id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Data guru berhasil diperbarui.',
                'teacher' => $teacher->load('user', 'subject_taught')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui data guru.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus guru
     */
    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->user->delete();

            return response()->json(['message' => 'Guru berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus guru.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar subject untuk dropdown
     */
    public function getSubjects()
    {
        $subjects = Subject::all();

        return response()->json([
            'message' => 'Daftar mata pelajaran berhasil diambil.',
            'subjects' => $subjects
        ]);
    }
}
