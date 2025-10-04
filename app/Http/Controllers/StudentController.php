<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar semua siswa.
     */
    public function index()
    {
        // Mengambil semua siswa beserta data user dan kelas terkait
        $students = Student::with('user', 'class')->get();

        return response()->json($students);
    }

    /**
     * Menyimpan siswa baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk dari frontend
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nis' => 'required|string|unique:students,nis',
            'class_id' => 'required|exists:classes,id',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_phone_number' => 'nullable|string',
        ]);
        
        DB::beginTransaction();

        try {
            // 2. Buat entri baru di tabel 'users'
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make('password123'), // Atur password default yang aman
                'role' => 'student',
            ]);

            // 3. Buat entri baru di tabel 'students' menggunakan ID dari user yang baru dibuat
            $student = Student::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'class_id' => $request->class_id,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'parent_phone_number' => $request->parent_phone_number,
            ]);

            DB::commit();

            // Mengembalikan respons sukses dengan data siswa dan user terkait
            return response()->json([
                'message' => 'Siswa berhasil ditambahkan.',
                'student' => $student->load('user', 'class')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menambahkan siswa.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui data siswa yang ada.
     */
    public function update(Request $request, Student $student)
    {
        // 1. Validasi data
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($student->user->id)],
            'nis' => ['required', 'string', Rule::unique('students', 'nis')->ignore($student->id)],
            'class_id' => 'required|exists:classes,id',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_phone_number' => 'nullable|string',
        ]);
        
        DB::beginTransaction();

        try {
            // 2. Perbarui data di tabel 'users'
            $student->user->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
            ]);

            // 3. Perbarui data di tabel 'students'
            $student->update([
                'nis' => $request->nis,
                'class_id' => $request->class_id,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'parent_phone_number' => $request->parent_phone_number,
            ]);

            DB::commit();

            // Mengembalikan respons sukses
            return response()->json([
                'message' => 'Data siswa berhasil diperbarui.',
                'student' => $student->load('user', 'class')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui data siswa.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus siswa dari database.
     */
    public function destroy(Student $student)
    {
        try {
            // Menghapus data user terkait akan otomatis menghapus data siswa karena onDelete('cascade')
            $student->user->delete();

            return response()->json(['message' => 'Siswa berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus siswa.', 'error' => $e->getMessage()], 500);
        }
    }
}