<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Direction;
use App\Models\Group;
use App\Models\AcademicYear;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directions = Direction::all();
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            // Agar faol o'quv yili bo'lmasa, yangi yaratamiz
            $academicYear = AcademicYear::create([
                'name' => '2025-2026',
                'start_date' => now()->year . '-09-01',
                'end_date' => (now()->year + 1) . '-07-15',
                'is_active' => true,
            ]);
        }

        foreach ($directions as $direction) {
            for ($i = 1; $i <= 3; $i++) {
                $course = rand(1, 4);
                Group::firstOrCreate([
                    'direction_id' => $direction->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => "Guruh {$direction->code}-{$i}",
                    'code' => "GRP-{$direction->id}-{$i}",
                    'course' => $course,
                    'student_count' => rand(15, 25),
                    'education_type' => ['kunduzgi', 'sirtqi', 'kechki','masofaviy'][rand(0, 3)],
                    'education_language' => ['uzbek', 'russian'][rand(0, 1)],
                ]);
            }
        }
    }
}
