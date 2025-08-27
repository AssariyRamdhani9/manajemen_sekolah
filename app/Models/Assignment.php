<?php

// app/Models/Assignment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_teacher_id',
        'title',
        'description',
        'due_date',
    ];

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class, 'class_subject_teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}