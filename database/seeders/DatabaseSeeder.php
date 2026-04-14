<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Rollar va Ruxsatlar
            RolePermissionSeeder::class,

            // 2. Universitet Strukturasi (Avval struktura!)
            FacultySeeder::class,
            DepartmentSeeder::class,
            DirectionSeeder::class,

            // 3. Tizim Foydalanuvchilari (Kafedralar yaratilgandan keyin!)
            UserSeeder::class,

            // 4. O'quv jarayoni elementlari
            GroupSeeder::class,
            SubjectSeeder::class,

            // 5. O'qituvchilar
            TeacherSeeder::class,
        ]);
    }
}
