<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class Student extends Model
{
    use HasFactory, Searchable;

    protected $searchable = ['name', 'nis', 'class.name'];

    protected $fillable = [
        'nis',
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'class_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getGenderFullAttribute()
    {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}