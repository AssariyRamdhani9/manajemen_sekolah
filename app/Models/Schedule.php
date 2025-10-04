<?php

// app/Models/Schedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
    ];

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class);
    }
}