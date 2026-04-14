<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole    = Role::where('name', 'admin')->first();
        $dekanRole    = Role::where('name', 'dekan')->first();
        $kafedraRole  = Role::where('name', 'kafedra_mudiri')->first();

        if (!($adminRole && $dekanRole && $kafedraRole)) {
            $this->command->error('Rollar topilmadi. RolePermissionSeeder\'ni tekshiring.');
            return;
        }

        // ADMIN
        User::firstOrCreate(
            ['email' => 'admin@yau.uz'],
            [
                'name'               => 'Tizim Admini',
                'password'           => Hash::make('password'),
                'role_id'            => $adminRole->id,
                'phone'              => '+998901234567',
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );

        // DEKAN
        $faculty = Faculty::where('name', 'Yangi Asr Universiteti')->first();
        $dekanUser = User::firstOrCreate(
            ['email' => 'dekan@yau.uz'],
            [
                'name'               => 'Tursunov Abdulaziz Karimovich',
                'password'           => Hash::make('password'),
                'role_id'            => $dekanRole->id,
                'phone'              => '+998901234568',
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );
        if ($faculty && !$faculty->dean_id) {
            $faculty->update(['dean_id' => $dekanUser->id]);
        }

        // KAFEDRA MUDIRLARI - har bir kafedraga
        $kafedraHeads = [
            'Maxsus pedagogika kafedrasi'             => ['name' => 'Qodirov Mansur Salimovich',    'email' => 'kafedra.mpk@yau.uz'],
            "Umumta'lim fanlari kafedrasi"            => ['name' => 'Tosheva Hulkar Baxtiyorovna',  'email' => 'kafedra.ufk@yau.uz'],
            'Tillar kafedrasi'                        => ['name' => 'Hasanov Jamshid Normatovich',  'email' => 'kafedra.tk@yau.uz'],
            "Maktab va maktabgacha ta'lim kafedrasi"  => ['name' => 'Norova Zulfiya Rahimovna',     'email' => 'kafedra.mmk@yau.uz'],
            'Mumtoz sharq filologiyasi kafedrasi'     => ['name' => 'Xoliqov Behruz Alimovich',    'email' => 'kafedra.msf@yau.uz'],
            'Sharq filologiyasi kafedrasi'            => ['name' => 'Yusupov Sardor Hamidovich',   'email' => 'kafedra.sfk@yau.uz'],
        ];

        foreach ($kafedraHeads as $deptName => $head) {
            $department = Department::where('name', $deptName)->first();
            if (!$department) continue;

            $user = User::firstOrCreate(
                ['email' => $head['email']],
                [
                    'name'               => $head['name'],
                    'password'           => Hash::make('password'),
                    'role_id'            => $kafedraRole->id,
                    'is_active'          => true,
                    'email_verified_at'  => now(),
                ]
            );

            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id'  => $department->id,
                    'position'       => 'Kafedra mudiri',
                    'employment_type'=> 'main_job',
                    'is_active'      => true,
                ]
            );

            // Kafedraga mudirni belgilash
            if (!$department->head_id) {
                $department->update(['head_id' => $user->id]);
            }
        }

        $this->command->info('UserSeeder: Admin, Dekan va Kafedra mudirlari yaratildi.');
    }
}
