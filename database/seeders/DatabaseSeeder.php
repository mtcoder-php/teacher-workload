<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Rollar va Ruxsatlar
            RolePermissionSeeder::class,

            // 2. Tizim Foydalanuvchilari (Admin, Kafedra Mudiri)
            UserSeeder::class,

            // 3. Universitet Strukturasi
            FacultySeeder::class,
            DepartmentSeeder::class,
            DirectionSeeder::class,

            // 4. O'quv jarayoni elementlari
            GroupSeeder::class,      // AcademicYear'ni o'z ichida yaratadi
            SubjectSeeder::class,
        ]);
    }
}
