<?php

// app/Models/Teacher.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'subject_taught_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classSubjectTeachers()
    {
        return $this->hasMany(ClassSubjectTeacher::class, 'teacher_id');
    }
    
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }

    public function subject_taught()
    {
        return $this->belongsTo(Subject::class, 'subject_taught_id');
    }

    // 🔥 Tambahkan ini untuk ambil semua jadwal guru
    public function schedules()
    {
        return $this->hasManyThrough(
            Schedule::class,
            ClassSubjectTeacher::class,
            'teacher_id', // FK di class_subject_teachers
            'class_subject_teacher_id', // FK di schedules
            'id', // PK di teachers
            'id'  // PK di class_subject_teachers
        );
    }
}