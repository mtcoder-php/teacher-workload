<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Direction;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directions = Direction::all();

        foreach ($directions as $direction) {
            for ($i = 1; $i <= 5; $i++) {
                Subject::firstOrCreate(
                // Qidirish uchun shartlar
                    [
                        'department_id' => $direction->department_id,
                        'direction_id' => $direction->id,
                        'name' => "{$direction->name} fani {$i}",
                        'code' => "SUB-{$direction->id}-{$i}",
                    ],
                    // Agar topilmasa, quyidagi ma'lumotlar bilan yaratish
                    [
                        // ✅ YETISHMAYOTGAN MAYDON QO'SHILDI
                        'credit_hours' => rand(3, 6), // Tasodifiy kredit soati (3 dan 6 gacha)

                        // Soatlarni tasodifiy generatsiya qilish
                        'semester_1_lecture' => rand(10, 30),
                        'semester_1_practical' => rand(20, 40),
                        'semester_2_lecture' => rand(10, 30),
                        'semester_2_practical' => rand(20, 40),
                        // ... (Agar boshqa majburiy maydonlar bo'lsa, shu yerga qo'shish kerak)
                    ]
                );
            }
        }
    }
}
