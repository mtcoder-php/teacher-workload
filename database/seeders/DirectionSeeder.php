<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Direction;

class DirectionSeeder extends Seeder
{
    public function run(): void
    {
        // Har bir kafedraga mos yo'nalishlar
        $data = [
            'Maxsus pedagogika kafedrasi' => [
                ['name' => "Boshlang'ich ta'lim va sport tarbiyaviy ish",  'code' => 'BTS',  'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Maktabgacha ta\'lim',                         'code' => 'MT',   'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Defektologiya',                                'code' => 'DEF',  'degree_type' => 'bakalavr', 'duration_years' => 4],
            ],
            "Umumta'lim fanlari kafedrasi" => [
                ['name' => 'Matematika',                                   'code' => 'MAT',  'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Fizika',                                       'code' => 'FIZ',  'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Biologiya',                                    'code' => 'BIO',  'degree_type' => 'bakalavr', 'duration_years' => 4],
            ],
            'Tillar kafedrasi' => [
                ['name' => "O'zbek tili va adabiyoti",                     'code' => 'OTA',  'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Ingliz tili va adabiyoti',                     'code' => 'ITA',  'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Rus tili va adabiyoti',                        'code' => 'RTA',  'degree_type' => 'bakalavr', 'duration_years' => 4],
            ],
            "Maktab va maktabgacha ta'lim kafedrasi" => [
                ['name' => "Maktabgacha ta'lim (magistratura)",            'code' => 'MTM',  'degree_type' => 'magistratura', 'duration_years' => 2],
                ['name' => "Boshlang'ich ta'lim metodikasi",               'code' => 'BTM',  'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => "Ta'lim menejmenti",                            'code' => 'TM',   'degree_type' => 'bakalavr', 'duration_years' => 4],
            ],
            'Mumtoz sharq filologiyasi kafedrasi' => [
                ['name' => 'Arab tili va adabiyoti',                       'code' => 'ARAB', 'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Fors tili va adabiyoti',                       'code' => 'FORS', 'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Mumtoz adabiyot',                              'code' => 'MA',   'degree_type' => 'magistratura', 'duration_years' => 2],
            ],
            'Sharq filologiyasi kafedrasi' => [
                ['name' => 'Xitoy tili va adabiyoti',                      'code' => 'XITA', 'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Yapon tili va adabiyoti',                      'code' => 'YATA', 'degree_type' => 'bakalavr', 'duration_years' => 4],
                ['name' => 'Sharqshunoslik',                               'code' => 'SH',   'degree_type' => 'magistratura', 'duration_years' => 2],
            ],
        ];

        foreach ($data as $deptName => $directions) {
            $department = Department::where('name', $deptName)->first();
            if (!$department) {
                $this->command->warn("Kafedra topilmadi: {$deptName}");
                continue;
            }

            foreach ($directions as $dir) {
                Direction::firstOrCreate(
                    ['code' => $dir['code']],
                    [
                        'name'           => $dir['name'],
                        'code'           => $dir['code'],
                        'department_id'  => $department->id,
                        'degree_type'    => $dir['degree_type'],
                        'duration_years' => $dir['duration_years'],
                        'is_active'      => true,
                    ]
                );
            }
        }

        $this->command->info('DirectionSeeder: Yo\'nalishlar yaratildi.');
    }
}
