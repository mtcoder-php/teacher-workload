<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Role; // ✅ Role modelini import qilish juda muhim!
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. "O'qituvchi" rolini ma'lumotlar bazasidan topib olamiz
        $oqituvchiRole = Role::where('name', 'oqituvchi')->first();

        // Agar "oqituvchi" roli topilmasa, seeder ishini to'xtatamiz
        if (!$oqituvchiRole) {
            $this->command->error('"oqituvchi" roli topilmadi. Iltimos, RolePermissionSeeder to\'g\'ri ishlaganini tekshiring.');
            return;
        }

        $departments = Department::all();

        foreach ($departments as $department) {
            for ($i = 1; $i <= 5; $i++) {
                $userName = "O'qituvchi {$department->id}-{$i}";

                // 2. User yaratishda 'role_id' ni to'g'ridan-to'g'ri beramiz
                $user = User::firstOrCreate(
                    ['email' => "teacher{$department->id}_{$i}@example.com"],
                    [
                        'name' => $userName,
                        'password' => Hash::make('password'),
                        'role_id' => $oqituvchiRole->id, // <<<--- ENG MUHIM TUZATISH
                        'phone' => '+99890' . rand(1000000, 9999999),
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                // 3. O'qituvchi profilini yaratish
                Teacher::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id' => $department->id,
                        'position' => 'O\'qituvchi',
                    ]
                );

                // 4. `assignRole()` metodi endi ISHLATILMAYDI
            }
        }
    }
}
