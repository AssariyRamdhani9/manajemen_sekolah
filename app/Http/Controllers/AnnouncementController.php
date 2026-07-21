<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Menampilkan pengumuman yang relevan untuk user yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        
        $announcements = Announcement::query()
            ->when($user->role === 'teacher', function ($query) use ($user) {
                // Tampilkan pengumuman untuk guru: semua pengumuman sekolah,
                // dan pengumuman khusus untuk mata pelajaran atau kelasnya.
                $query->where('target_audience', 'all_school')
                      ->orWhere(function ($q) use ($user) {
                          $teacher = $user->teacher;
                          $classIds = $teacher->classSubjectTeachers->pluck('class_id');
                          $subjectIds = $teacher->classSubjectTeachers->pluck('subject_id');
                          $q->where('target_audience', 'specific_class')->whereIn('class_id', $classIds)
                            ->orWhere('target_audience', 'specific_subject')->whereIn('subject_id', $subjectIds);
                      });
            })
            ->when($user->role === 'student', function ($query) use ($user) {
                // Tampilkan pengumuman untuk siswa: semua pengumuman sekolah,
                // dan pengumuman khusus untuk kelasnya.
                $query->where('target_audience', 'all_school')
                      ->orWhere(function ($q) use ($user) {
                          $student = $user->student;
                          $q->where('target_audience', 'specific_class')->where('class_id', $student->class_id);
                      });
            })
            ->with('user', 'class', 'subject')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($announcements);
    }
    
    /**
     * Menyimpan pengumuman baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all_school,specific_class,specific_subject',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);
        
        // Pastikan class_id atau subject_id diisi jika targetnya spesifik
        if ($request->target_audience !== 'all_school') {
            if ($request->target_audience === 'specific_class' && !$request->class_id) {
                 return response()->json(['message' => 'Class ID is required for this audience.'], 422);
            }
            if ($request->target_audience === 'specific_subject' && !$request->subject_id) {
                 return response()->json(['message' => 'Subject ID is required for this audience.'], 422);
            }
        }
        
        $announcement = Announcement::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
        ]);
        
        return response()->json(['message' => 'Pengumuman berhasil dipublikasikan.', 'announcement' => $announcement], 201);
    }

    // Metode update() dan destroy() bisa ditambahkan di sini, mirip dengan controller lain.
    // Pastikan user yang mengedit/menghapus adalah pembuat pengumuman atau admin.
}