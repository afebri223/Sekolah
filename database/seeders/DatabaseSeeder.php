<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Attendance;

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
            'name' => 'Dr. Ahmad Wahyudi, M.Pd',
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
        $waliKelas1 = User::create([
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'budi@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'is_wali_kelas' => true,
            'nip' => '444555666',
            'phone' => '081234567893',
        ]);

        $waliKelas2 = User::create([
            'name' => 'Dewi Sartika, S.Pd',
            'email' => 'dewi@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'is_wali_kelas' => true,
            'nip' => '777888999',
            'phone' => '081234567894',
        ]);

        // Create more teachers
        $teachers = [
            ['name' => 'Andi Wijaya, S.Pd', 'email' => 'andi@sekolah.com', 'nip' => '111111111'],
            ['name' => 'Maya Indah, S.Pd', 'email' => 'maya@sekolah.com', 'nip' => '222222222'],
            ['name' => 'Rino Pratama, S.Pd', 'email' => 'rino@sekolah.com', 'nip' => '333333333'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'nip' => $teacher['nip'],
                'phone' => '081234567890',
            ]);
        }

        // Create Classes
        $classes = [
            ['name' => '10A', 'grade' => '10', 'major' => 'IPA', 'wali_kelas_id' => $waliKelas1->id, 'capacity' => 32],
            ['name' => '10B', 'grade' => '10', 'major' => 'IPS', 'wali_kelas_id' => $waliKelas2->id, 'capacity' => 30],
            ['name' => '11IPA1', 'grade' => '11', 'major' => 'IPA', 'capacity' => 30],
            ['name' => '11IPS1', 'grade' => '11', 'major' => 'IPS', 'capacity' => 28],
            ['name' => '12IPA1', 'grade' => '12', 'major' => 'IPA', 'capacity' => 25],
            ['name' => '12IPS1', 'grade' => '12', 'major' => 'IPS', 'capacity' => 27],
        ];

        $createdClasses = [];
        foreach ($classes as $classData) {
            $createdClasses[] = Classes::create($classData);
        }

        // Create Students
        $studentNames = [
            // 10A Students (IPA)
            ['nis' => '2024001', 'name' => 'Andi Pratama Putra', 'gender' => 'L', 'class_idx' => 0],
            ['nis' => '2024002', 'name' => 'Sari Dewi Lestari', 'gender' => 'P', 'class_idx' => 0],
            ['nis' => '2024003', 'name' => 'Riko Saputra Wijaya', 'gender' => 'L', 'class_idx' => 0],
            ['nis' => '2024004', 'name' => 'Maya Sari Indah', 'gender' => 'P', 'class_idx' => 0],
            ['nis' => '2024005', 'name' => 'Dani Firmansyah', 'gender' => 'L', 'class_idx' => 0],
            ['nis' => '2024006', 'name' => 'Lina Marlina Sari', 'gender' => 'P', 'class_idx' => 0],
            ['nis' => '2024007', 'name' => 'Agus Salim Harahap', 'gender' => 'L', 'class_idx' => 0],
            ['nis' => '2024008', 'name' => 'Fitri Handayani', 'gender' => 'P', 'class_idx' => 0],
            ['nis' => '2024009', 'name' => 'Yoga Pratama Sari', 'gender' => 'L', 'class_idx' => 0],
            ['nis' => '2024010', 'name' => 'Dewi Sartika Ningrum', 'gender' => 'P', 'class_idx' => 0],
            
            // 10B Students (IPS)
            ['nis' => '2024011', 'name' => 'Ahmad Fauzi Rahman', 'gender' => 'L', 'class_idx' => 1],
            ['nis' => '2024012', 'name' => 'Nurul Aisyah Putri', 'gender' => 'P', 'class_idx' => 1],
            ['nis' => '2024013', 'name' => 'Bayu Setiawan', 'gender' => 'L', 'class_idx' => 1],
            ['nis' => '2024014', 'name' => 'Rina Wulandari', 'gender' => 'P', 'class_idx' => 1],
            ['nis' => '2024015', 'name' => 'Fajar Nugroho', 'gender' => 'L', 'class_idx' => 1],
            ['nis' => '2024016', 'name' => 'Indah Permata Sari', 'gender' => 'P', 'class_idx' => 1],
            ['nis' => '2024017', 'name' => 'Rizki Ramadan', 'gender' => 'L', 'class_idx' => 1],
            ['nis' => '2024018', 'name' => 'Sinta Dewi', 'gender' => 'P', 'class_idx' => 1],
            
            // 11IPA1 Students
            ['nis' => '2023001', 'name' => 'Arif Budiman', 'gender' => 'L', 'class_idx' => 2],
            ['nis' => '2023002', 'name' => 'Citra Kirana', 'gender' => 'P', 'class_idx' => 2],
            ['nis' => '2023003', 'name' => 'Dimas Pratama', 'gender' => 'L', 'class_idx' => 2],
            ['nis' => '2023004', 'name' => 'Eka Rahayu', 'gender' => 'P', 'class_idx' => 2],
            
            // 12IPA1 Students  
            ['nis' => '2022001', 'name' => 'Fadli Zon Nasution', 'gender' => 'L', 'class_idx' => 4],
            ['nis' => '2022002', 'name' => 'Gita Savitri', 'gender' => 'P', 'class_idx' => 4],
            ['nis' => '2022003', 'name' => 'Hendra Setiawan', 'gender' => 'L', 'class_idx' => 4],
        ];

        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Yogyakarta', 'Semarang', 'Palembang', 'Makassar'];
        $addresses = [
            'Jl. Merdeka No. 123, RT 01/RW 05',
            'Jl. Sudirman No. 456, RT 02/RW 03', 
            'Jl. Gatot Subroto No. 789, RT 03/RW 07',
            'Jl. Diponegoro No. 321, RT 04/RW 02',
            'Jl. Ahmad Yani No. 654, RT 05/RW 01'
        ];

        foreach ($studentNames as $index => $studentData) {
            $birthDate = now()->subYears(rand(15, 18))->subDays(rand(1, 365))->format('Y-m-d');
            $city = $cities[array_rand($cities)];
            $address = $addresses[array_rand($addresses)] . ', ' . $city;
            
            $student = Student::create([
                'nis' => $studentData['nis'],
                'name' => $studentData['name'],
                'gender' => $studentData['gender'],
                'birth_date' => $birthDate,
                'birth_place' => $city,
                'address' => $address,
                'phone' => '08' . rand(1000000000, 9999999999),
                'parent_name' => 'Orang Tua ' . explode(' ', $studentData['name'])[0],
                'parent_phone' => '08' . rand(1000000000, 9999999999),
                'class_id' => $createdClasses[$studentData['class_idx']]->id,
                'status' => 'active',
            ]);

            // Create some sample attendance records for the first two classes
            if ($studentData['class_idx'] < 2) {
                // Generate attendance for the last 30 days
                for ($i = 0; $i < 30; $i++) {
                    $date = now()->subDays($i);
                    
                    // Skip weekends
                    if ($date->isWeekend()) continue;
                    
                    // 85% chance of attendance
                    if (rand(1, 100) <= 85) {
                        $statuses = ['hadir', 'izin', 'sakit', 'alpha'];
                        $weights = [80, 10, 7, 3]; // Weighted random
                        
                        $random = rand(1, 100);
                        $status = 'hadir';
                        if ($random > 80) $status = 'izin';
                        if ($random > 90) $status = 'sakit';  
                        if ($random > 97) $status = 'alpha';
                        
                        $notes = null;
                        if ($status !== 'hadir') {
                            $noteOptions = [
                                'izin' => ['Keperluan keluarga', 'Acara keluarga', 'Izin pribadi'],
                                'sakit' => ['Demam', 'Flu', 'Sakit perut', 'Pusing'],
                                'alpha' => [null, null, 'Terlambat', null]
                            ];
                            $notes = $noteOptions[$status][array_rand($noteOptions[$status])];
                        }

                        Attendance::create([
                            'student_id' => $student->id,
                            'class_id' => $student->class_id,
                            'date' => $date->format('Y-m-d'),
                            'status' => $status,
                            'notes' => $notes,
                            'recorded_by' => $studentData['class_idx'] == 0 ? $waliKelas1->id : $waliKelas2->id,
                        ]);
                    }
                }
            }
        }

        echo "\n=== DATABASE SEEDER COMPLETED ===\n";
        echo "✅ Admin users created\n";
        echo "✅ " . count($classes) . " classes created\n";
        echo "✅ " . count($studentNames) . " students created\n";
        echo "✅ Sample attendance records generated\n\n";
        
        echo "🔐 LOGIN CREDENTIALS:\n";
        echo "👤 Admin: admin@sekolah.com / password123\n";
        echo "👔 Kepala Sekolah: kepala@sekolah.com / password123\n";  
        echo "👨‍🏫 Wali Kelas (10A): budi@sekolah.com / password123\n";
        echo "👩‍🏫 Wali Kelas (10B): dewi@sekolah.com / password123\n";
        echo "🧑‍🏫 Guru: siti@sekolah.com / password123\n\n";
        
        echo "🚀 Ready to use! Run: php artisan serve\n";
    }
}