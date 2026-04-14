<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        Faculty::firstOrCreate(
            ['name' => 'Yangi Asr Universiteti'],
            [
                'code'      => 'YAU',
                'name'      => 'Yangi Asr Universiteti',
                'is_active' => true,
            ]
        );

        $this->command->info('FacultySeeder: Yangi Asr Universiteti yaratildi.');
    }
}
