<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Searchable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    protected $searchable = ['name', 'email', 'nip'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_wali_kelas',
        'nip',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_wali_kelas' => 'boolean',
        ];
    }

    // Relationship
    public function waliKelas()
    {
        return $this->hasOne(Classes::class, 'wali_kelas_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKepalaSekolah()
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isWaliKelas()
    {
        return $this->is_wali_kelas && $this->waliKelas;
    }
    

    public function getRoleName()
    {
        $roles = [
            'admin' => 'Administrator',
            'kepala_sekolah' => 'Kepala Sekolah',
            'guru' => 'Guru'
        ];

        return $roles[$this->role] ?? 'Unknown';
    }
}