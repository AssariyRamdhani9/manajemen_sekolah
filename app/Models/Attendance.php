<?php

// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_subject_teacher_id',
        'date',
        'status',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class);
    }
}