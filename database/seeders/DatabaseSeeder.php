<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classes;
use App\Models\Student;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'nip' => '123456789',
            'phone' => '081234567890',
        ]);

        // Create Kepala Sekolah
        $kepalaSekolah = User::create([
            'name' => 'Dr. Ahmad Wahyudi',
            'email' => 'kepala@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'kepala_sekolah',
            'nip' => '987654321',
            'phone' => '081234567891',
        ]);

        // Create Guru Biasa
        $guru1 = User::create([
            'name' => 'Siti Nurhaliza, S.Pd',
            'email' => 'siti@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'nip' => '111222333',
            'phone' => '081234567892',
        ]);

        // Create Wali Kelas
        $waliKelas = User::create([
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'budi@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'is_wali_kelas' => true,
            'nip' => '444555666',
            'phone' => '081234567893',
        ]);

        // Create Kelas
        $kelas10A = Classes::create([
            'name' => '10A',
            'grade' => '10',
            'major' => 'IPA',
            'wali_kelas_id' => $waliKelas->id,
            'capacity' => 30,
        ]);

        $kelas10B = Classes::create([
            'name' => '10B',
            'grade' => '10',
            'major' => 'IPS',
            'capacity' => 30,
        ]);

        // Create Students
        $students = [
            ['nis' => '2024001', 'name' => 'Andi Pratama', 'gender' => 'L'],
            ['nis' => '2024002', 'name' => 'Sari Dewi', 'gender' => 'P'],
            ['nis' => '2024003', 'name' => 'Riko Saputra', 'gender' => 'L'],
            ['nis' => '2024004', 'name' => 'Maya Sari', 'gender' => 'P'],
            ['nis' => '2024005', 'name' => 'Dani Firmansyah', 'gender' => 'L'],
            ['nis' => '2024006', 'name' => 'Lina Marlina', 'gender' => 'P'],
            ['nis' => '2024007', 'name' => 'Agus Salim', 'gender' => 'L'],
            ['nis' => '2024008', 'name' => 'Fitri Handayani', 'gender' => 'P'],
            ['nis' => '2024009', 'name' => 'Yoga Pratama', 'gender' => 'L'],
            ['nis' => '2024010', 'name' => 'Dewi Sartika', 'gender' => 'P'],
        ];

        foreach ($students as $studentData) {
            Student::create([
                'nis' => $studentData['nis'],
                'name' => $studentData['name'],
                'gender' => $studentData['gender'],
                'birth_date' => '2008-01-01',
                'birth_place' => 'Jakarta',
                'address' => 'Jl. Contoh No. 123, Jakarta',
                'phone' => '081234567890',
                'parent_name' => 'Orang Tua ' . $studentData['name'],
                'parent_phone' => '081234567890',
                'class_id' => $kelas10A->id,
                'status' => 'active',
            ]);
        }

        echo "Seeder berhasil dijalankan!\n";
        echo "Login credentials:\n";
        echo "Admin: admin@sekolah.com / password123\n";
        echo "Kepala Sekolah: kepala@sekolah.com / password123\n";
        echo "Guru: siti@sekolah.com / password123\n";
        echo "Wali Kelas: budi@sekolah.com / password123\n";
    }
}