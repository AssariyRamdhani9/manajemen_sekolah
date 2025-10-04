<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    /**
     * Menampilkan daftar semua kelas.
     */
    public function index()
    {
        $classes = Classes::all();
        return response()->json($classes);
    }

    /**
     * Menyimpan kelas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:classes,name',
            'level' => 'nullable|string',
            'major' => 'nullable|string',
        ]);

        $class = Classes::create($request->all());

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan.',
            'class' => $class
        ], 201);
    }

    /**
     * Memperbarui data kelas yang ada.
     */
    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('classes', 'name')->ignore($class->id)],
            'level' => 'nullable|string',
            'major' => 'nullable|string',
        ]);

        $class->update($request->all());

        return response()->json([
            'message' => 'Kelas berhasil diperbarui.',
            'class' => $class
        ]);
    }

    /**
     * Menghapus kelas dari database.
     */
    public function destroy(Classes $class)
    {
        try {
            $class->delete();
            return response()->json(['message' => 'Kelas berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus kelas. Pastikan kelas tidak memiliki siswa terkait.'], 500);
        }
    }
}