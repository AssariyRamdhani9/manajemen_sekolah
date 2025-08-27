<?php

// app/Models/Subject.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'subject_taught_id');
    }

    public function classSubjectTeachers()
    {
        return $this->hasMany(ClassSubjectTeacher::class, 'subject_id');
    }
}