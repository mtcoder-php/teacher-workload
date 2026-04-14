<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Direction;
use App\Models\Group;
use App\Models\AcademicYear;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        // Joriy o'quv yili
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::create([
                'name'       => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date'   => '2026-07-15',
                'is_active'  => true,
            ]);
        }

        // Har bir yo'nalish uchun guruhlar (har kursdan 1-2 ta)
        $directions = Direction::all();

        foreach ($directions as $direction) {
            $maxCourse = $direction->duration_years ?? 4;
            $prefix    = strtoupper(substr($direction->code, 0, 3));

            for ($course = 1; $course <= $maxCourse; $course++) {
                // Har kursdan 2 ta guruh
                for ($g = 1; $g <= 2; $g++) {
                    $year  = 25 - ($course - 1); // 2025 yil matricula
                    $name  = "{$prefix}-{$year}-{$g}";
                    $code  = "{$direction->code}{$course}{$g}";

                    Group::firstOrCreate(
                        ['code' => $code],
                        [
                            'name'             => $name,
                            'code'             => $code,
                            'direction_id'     => $direction->id,
                            'academic_year_id' => $academicYear->id,
                            'course'           => $course,
                            'student_count'    => rand(18, 28),
                            'education_type'   => 'kunduzgi',
                            'education_language' => 'uzbek',
                            'is_active'        => true,
                        ]
                    );
                }
            }
        }

        $this->command->info('GroupSeeder: Guruhlar yaratildi.');
    }
}
