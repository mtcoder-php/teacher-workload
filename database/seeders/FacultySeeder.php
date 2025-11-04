<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faculty::firstOrCreate(
        // Qidirish uchun shart
            ['name' => 'Yangi Asr Universiteti'],
            // Agar topilmasa, quyidagi ma'lumotlar bilan yaratish
            [
                'code' => 'YAU', // ✅ YETISHMAYOTGAN MAYDON QO'SHILDI
                'name' => 'Yangi Asr Universiteti'
            ]
        );
    }
}
