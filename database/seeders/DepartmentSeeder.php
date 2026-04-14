<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $faculty = Faculty::where('name', 'Yangi Asr Universiteti')->first();

        if (!$faculty) {
            $this->command->error('Yangi Asr Universiteti topilmadi!');
            return;
        }

        $departments = [
            ['name' => 'Maxsus pedagogika kafedrasi',          'code' => 'MPK'],
            ['name' => 'Umumta\'lim fanlari kafedrasi',        'code' => 'UFK'],
            ['name' => 'Tillar kafedrasi',                     'code' => 'TK'],
            ['name' => 'Maktab va maktabgacha ta\'lim kafedrasi', 'code' => 'MMK'],
            ['name' => 'Mumtoz sharq filologiyasi kafedrasi',  'code' => 'MSF'],
            ['name' => 'Sharq filologiyasi kafedrasi',         'code' => 'SFK'],
        ];

        foreach ($departments as $data) {
            Department::firstOrCreate(
                ['name' => $data['name']],
                [
                    'faculty_id' => $faculty->id,
                    'name'       => $data['name'],
                    'code'       => $data['code'],
                    'is_active'  => true,
                ]
            );
        }

        $this->command->info('DepartmentSeeder: 6 ta kafedra yaratildi.');
    }
}
