<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Menangani proses pendaftaran user baru.
     */
   public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa', // default siswa, atau bisa sesuai input
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil!');
    }

    //Login
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'role' => 'required|string',
    ]);

    // Cek email+password
    if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
        return response()->json([
            'message' => 'Email atau password salah!'
        ], 401);
    }

    $user = Auth::user();

    // Cek role
    if ($user->role !== $credentials['role']) {
        return response()->json([
            'message' => 'Role tidak sesuai!'
        ], 403);
    }

    // Token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login berhasil',
        'user' => $user,
        'token' => $token
    ]);
}



    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

}
