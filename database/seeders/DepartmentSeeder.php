<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Support\Str; // ✅ Str helperini import qilamiz

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculty = Faculty::first();

        if ($faculty) {
            $departments = [
                'Maxsus pedagogika kafedrasi',
                'Sharq filologiyasi kafedrasi',
                'Tillar kafedrasi',
                'Umumta’lim fanlari kafedrasi',
                'Maktab va maktabgacha ta’lim kafedrasi',
                'Mumtoz sharq filologiyasi kafedrasi',
            ];

            foreach ($departments as $departmentName) {
                Department::firstOrCreate(
                // Qidirish uchun shart
                    ['name' => $departmentName],
                    // Agar topilmasa, quyidagi ma'lumotlar bilan yaratish
                    [
                        'faculty_id' => $faculty->id,
                        'name' => $departmentName,
                        // ✅ YETISHMAYOTGAN MAYDON QO'SHILDI
                        // Natija: 'maxsus-pedagogika-kafedrasi' kabi kod generatsiya bo'ladi
                        'code' => Str::slug($departmentName)
                    ]
                );
            }
        }
    }
}
