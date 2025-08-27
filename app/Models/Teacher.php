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
        return $this->hasMany(ClassSubjectTeacher::class);
    }
    
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }
}