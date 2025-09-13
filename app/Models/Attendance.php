<?php
// app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}