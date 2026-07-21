<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_teacher_id',
        'title',
        'exam_type',
        'description',
        'duration_minutes',
        'start_time',
        'end_time',
        'is_published',
    ];

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
