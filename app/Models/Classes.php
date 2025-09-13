<?php
// app/Models/Classes.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade',
        'major',
        'wali_kelas_id',
        'capacity',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
}