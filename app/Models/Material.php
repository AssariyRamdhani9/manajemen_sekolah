<?php

// app/Models/Material.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_teacher_id',
        'title',
        'description',
        'file_url',
        'file_type',
    ];

    public function classSubjectTeacher()
    {
        return $this->belongsTo(ClassSubjectTeacher::class, 'class_subject_teacher_id');
    }
}