<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Direction;

class DirectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        foreach ($departments as $department) {
            for ($i = 1; $i <= 3; $i++) {
                Direction::firstOrCreate([
                    'department_id' => $department->id,
                    'name' => "{$department->name} yo'nalishi {$i}",
                    'code' => "DIR-{$department->id}-{$i}",
                    'degree_type' => ($i % 2 == 0) ? 'magistratura' : 'bakalavr',
                ]);
            }
        }
    }
}
