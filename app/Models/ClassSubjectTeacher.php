<?php

// app/Models/ClassSubjectTeacher.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectTeacher extends Model
{
    use HasFactory;

    protected $table = 'class_subject_teacher';
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_subject_teacher_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'class_subject_teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_subject_teacher_id');
    }
}