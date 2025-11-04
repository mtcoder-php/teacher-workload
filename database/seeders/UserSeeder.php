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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Barcha kerakli rollarni topib olamiz
        $adminRole = Role::where('name', 'admin')->first();
        $dekanRole = Role::where('name', 'dekan')->first();
        $kafedraRole = Role::where('name', 'kafedra_mudiri')->first();
        $oqituvchiRole = Role::where('name', 'oqituvchi')->first();

        // Agar birorta rol topilmasa, xatolik berib to'xtatamiz
        if (!($adminRole && $dekanRole && $kafedraRole && $oqituvchiRole)) {
            $this->command->error('Barcha kerakli rollar (admin, dekan, kafedra_mudiri, oqituvchi) topilmadi. RolePermissionSeeder\'ni tekshiring.');
            return;
        }

        // ==========================================
        // ADMIN FOYDALANUVCHISI
        // ==========================================
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'phone' => '+998901234567',
                'is_active' => true,
            ]
        );

        // ==========================================
        // TEST UCHUN KERAKLI STRUKTURANI YARATISH
        // ==========================================
        $faculty = Faculty::firstOrCreate(['name' => 'Test Fakulteti'], ['code' => 'TEST-F']);

        $department1 = Department::firstOrCreate(
            ['name' => 'Dasturiy Injiniiring'],
            ['faculty_id' => $faculty->id, 'code' => 'DI']
        );

        $department2 = Department::firstOrCreate(
            ['name' => 'Tillar kafedrasi'],
            ['faculty_id' => $faculty->id, 'code' => 'TK']
        );

        // ==========================================
        // DEKAN FOYDALANUVCHISI
        // ==========================================
        $dekanUser = User::firstOrCreate(
            ['email' => 'dekan@example.com'],
            [
                'name' => 'Dekan Ahmedov',
                'password' => Hash::make('password'),
                'role_id' => $dekanRole->id,
                'phone' => '+998901234568',
                'is_active' => true,
            ]
        );
        // Kelajakda Dekan'ni fakultetga bog'lash uchun:
        // $faculty->dean_id = $dekanUser->id;
        // $faculty->save();

        // ==========================================
        // KAFEDRA MUDIRI FOYDALANUVCHISI (1-kafedraga)
        // ==========================================
        $kafedraMudirUser = User::firstOrCreate(
            ['email' => 'kafedra@example.com'],
            [
                'name' => 'Kafedra Mudiri Aliyev',
                'password' => Hash::make('password'),
                'role_id' => $kafedraRole->id,
                'phone' => '+998901234569',
                'is_active' => true,
            ]
        );
        // User'ni Teacher profiliga va kafedraga bog'lash
        Teacher::firstOrCreate(
            ['user_id' => $kafedraMudirUser->id],
            ['department_id' => $department1->id, 'position' => 'Kafedra mudiri']
        );

        // ==========================================
        // ODDIY O'QITUVCHI FOYDALANUVCHISI (2-kafedraga)
        // ==========================================
        $oqituvchiUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'O\'qituvchi Karimov',
                'password' => Hash::make('password'),
                'role_id' => $oqituvchiRole->id,
                'phone' => '+998901234570',
                'is_active' => true,
            ]
        );
        // User'ni Teacher profiliga va kafedraga bog'lash
        Teacher::firstOrCreate(
            ['user_id' => $oqituvchiUser->id],
            ['department_id' => $department2->id, 'position' => 'O\'qituvchi']
        );

        // ==========================================
        // BOSHQA KAFEDRA MUDIRI (2-kafedraga)
        // ==========================================
        $kafedraMudirUser2 = User::firstOrCreate(
            ['email' => 'kafedra2@example.com'],
            [
                'name' => 'Kafedra Mudiri Valiev',
                'password' => Hash::make('password'),
                'role_id' => $kafedraRole->id,
                'phone' => '+998901234571',
                'is_active' => true,
            ]
        );
        // User'ni Teacher profiliga va kafedraga bog'lash
        Teacher::firstOrCreate(
            ['user_id' => $kafedraMudirUser2->id],
            ['department_id' => $department2->id, 'position' => 'Kafedra mudiri']
        );
    }
}
