<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_subject_teacher_id',
        'semester',
        'academic_year',
        'assignment_score',
        'uts_score',
        'uas_score',
        'final_score',
        'letter_grade',
        'teacher_notes',
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
