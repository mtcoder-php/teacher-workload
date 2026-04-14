<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $oqituvchiRole = Role::where('name', 'oqituvchi')->first();
        if (!$oqituvchiRole) {
            $this->command->error('"oqituvchi" roli topilmadi!');
            return;
        }

        // Har bir kafedraga 5 ta o'qituvchi
        $teachersByDept = [
            'Maxsus pedagogika kafedrasi' => [
                ['name' => 'Ahmedov Anvar Baxtiyorovich',     'email' => 'ahmedov.anvar@yau.uz',     'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Toshmatova Dilnoza Hamidovna',    'email' => 'toshmatova.d@yau.uz',      'position' => 'Katta o\'qituvchi', 'degree' => null],
                ['name' => 'Xoliqov Sherzod Normatovich',    'email' => 'xoliqov.sh@yau.uz',        'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Mirzayeva Feruza Ulugbekovna',   'email' => 'mirzayeva.f@yau.uz',       'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Qodirov Jasur Salimovich',       'email' => 'qodirov.j@yau.uz',         'position' => 'O\'qituvchi',    'degree' => null],
            ],
            "Umumta'lim fanlari kafedrasi" => [
                ['name' => 'Nazarov Bobur Hamzaevich',       'email' => 'nazarov.b@yau.uz',         'position' => 'Professor',     'degree' => 'DSc'],
                ['name' => 'Umarova Zilola Rustamovna',      'email' => 'umarova.z@yau.uz',         'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Karimov Eldor Toshpulatovich',   'email' => 'karimov.e@yau.uz',         'position' => 'Katta o\'qituvchi', 'degree' => null],
                ['name' => 'Yusupova Malika Isroilovna',     'email' => 'yusupova.m@yau.uz',        'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Holmatov Sanjar Qodiralievich',  'email' => 'holmatov.s@yau.uz',        'position' => 'Dotsent',       'degree' => 'PhD'],
            ],
            'Tillar kafedrasi' => [
                ['name' => 'Rahimova Nodira Baxtiyor qizi',  'email' => 'rahimova.n@yau.uz',        'position' => 'Katta o\'qituvchi', 'degree' => null],
                ['name' => 'Sobirov Otabek Mansurovich',     'email' => 'sobirov.o@yau.uz',         'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Tursunova Gulnora Abdurahmanovna','email' => 'tursunova.g@yau.uz',      'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Haydarov Zafar Norboʻtaevich',  'email' => 'haydarov.z@yau.uz',        'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Ismoilova Shahnoza Toxirovna',   'email' => 'ismoilova.sh@yau.uz',      'position' => 'Katta o\'qituvchi', 'degree' => null],
            ],
            "Maktab va maktabgacha ta'lim kafedrasi" => [
                ['name' => 'Mamatova Sarvinoz Alimovna',     'email' => 'mamatova.s@yau.uz',        'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Razzaqov Husan Muxtorovich',     'email' => 'razzaqov.h@yau.uz',        'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Ergasheva Mohira Baxtiyorovna',  'email' => 'ergasheva.m@yau.uz',       'position' => 'Katta o\'qituvchi', 'degree' => null],
                ['name' => 'Saidov Akbar Solijonovich',      'email' => 'saidov.a@yau.uz',          'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Jalolov Doniyor Hamidovich',     'email' => 'jalolov.d@yau.uz',         'position' => 'O\'qituvchi',    'degree' => null],
            ],
            'Mumtoz sharq filologiyasi kafedrasi' => [
                ['name' => 'Qosimov Alisher Bahodirovich',   'email' => 'qosimov.a@yau.uz',         'position' => 'Professor',     'degree' => 'DSc'],
                ['name' => 'Norqoʻziyeva Hulkar Soatovna',  'email' => 'norquziyeva.h@yau.uz',     'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Tojiboyev Ibrohim Mansurovich', 'email' => 'tojiboyev.i@yau.uz',       'position' => 'Katta o\'qituvchi', 'degree' => null],
                ['name' => 'Xasanova Dilrabo Hamroyevna',   'email' => 'xasanova.d@yau.uz',        'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Yoʻldoshev Mirzo Qoraboyevich', 'email' => 'yuldoshev.m@yau.uz',       'position' => 'Dotsent',       'degree' => 'PhD'],
            ],
            'Sharq filologiyasi kafedrasi' => [
                ['name' => 'Muxtarov Firdavs Obidovich',     'email' => 'muxtarov.f@yau.uz',        'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Baxtiyorova Nargiza Xamzaevna',  'email' => 'baxtiyorova.n@yau.uz',     'position' => 'Katta o\'qituvchi', 'degree' => null],
                ['name' => 'Rajabov Ulugbek Shamsiyevich',   'email' => 'rajabov.u@yau.uz',         'position' => 'O\'qituvchi',    'degree' => null],
                ['name' => 'Sultanova Iroda Xolmatovna',     'email' => 'sultanova.i@yau.uz',       'position' => 'Dotsent',       'degree' => 'PhD'],
                ['name' => 'Nishonov Sarvar Abdullayevich',  'email' => 'nishonov.s@yau.uz',        'position' => 'O\'qituvchi',    'degree' => null],
            ],
        ];

        foreach ($teachersByDept as $deptName => $teachers) {
            $department = Department::where('name', $deptName)->first();
            if (!$department) {
                $this->command->warn("Kafedra topilmadi: {$deptName}");
                continue;
            }

            foreach ($teachers as $t) {
                $user = User::firstOrCreate(
                    ['email' => $t['email']],
                    [
                        'name'               => $t['name'],
                        'password'           => Hash::make('password'),
                        'role_id'            => $oqituvchiRole->id,
                        'is_active'          => true,
                        'email_verified_at'  => now(),
                    ]
                );

                Teacher::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id'   => $department->id,
                        'position'        => $t['position'],
                        'academic_degree' => $t['degree'],
                        'employment_type' => 'main_job',
                        'is_active'       => true,
                    ]
                );
            }
        }

        $this->command->info('TeacherSeeder: Har bir kafedraga 5 tadan o\'qituvchi yaratildi.');
    }
}
