<?php

// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'class_id',
        'date_of_birth',
        'address',
        'parent_phone_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}