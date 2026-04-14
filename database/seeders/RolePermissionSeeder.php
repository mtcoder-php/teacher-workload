<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // ROLLARNI YARATISH
        // ========================================
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Tizim ma\'muri - barcha huquqlar'
            ],
            [
                'name' => 'dekan',
                'display_name' => 'Dekan',
                'description' => 'Fakultet dekani - fakultet bo\'yicha to\'liq huquq'
            ],
            [
                'name' => 'kafedra_mudiri',
                'display_name' => 'Kafedra Mudiri',
                'description' => 'Kafedra boshlig\'i - kafedra bo\'yicha to\'liq huquq'
            ],
            [
                'name' => 'oqituvchi',
                'display_name' => 'O\'qituvchi',
                'description' => 'Oddiy o\'qituvchi - faqat o\'z ma\'lumotlarini ko\'rish'
            ],
            [
                'name' => 'nazoratchi',
                'display_name' => 'Nazoratchi',
                'description' => 'O\'quv bo\'limi xodimi - barcha ma\'lumotlarni ko\'rish, hisobot'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        // ========================================
        // RUXSATLARNI YARATISH
        // ========================================
        $permissions = [
            // Foydalanuvchilar
            ['name' => 'users.view', 'display_name' => 'Foydalanuvchilarni ko\'rish', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Foydalanuvchi qo\'shish', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Foydalanuvchini tahrirlash', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Foydalanuvchini o\'chirish', 'group' => 'users'],

            // Fakultetlar
            ['name' => 'faculties.view', 'display_name' => 'Fakultetlarni ko\'rish', 'group' => 'faculties'],
            ['name' => 'faculties.create', 'display_name' => 'Fakultet qo\'shish', 'group' => 'faculties'],
            ['name' => 'faculties.edit', 'display_name' => 'Fakultetni tahrirlash', 'group' => 'faculties'],
            ['name' => 'faculties.delete', 'display_name' => 'Fakultetni o\'chirish', 'group' => 'faculties'],

            // Kafedralar
            ['name' => 'departments.view', 'display_name' => 'Kafedralarni ko\'rish', 'group' => 'departments'],
            ['name' => 'departments.create', 'display_name' => 'Kafedra qo\'shish', 'group' => 'departments'],
            ['name' => 'departments.edit', 'display_name' => 'Kafedarni tahrirlash', 'group' => 'departments'],
            ['name' => 'departments.delete', 'display_name' => 'Kafedarni o\'chirish', 'group' => 'departments'],

            // O'qituvchilar
            ['name' => 'teachers.view', 'display_name' => 'O\'qituvchilarni ko\'rish', 'group' => 'teachers'],
            ['name' => 'teachers.create', 'display_name' => 'O\'qituvchi qo\'shish', 'group' => 'teachers'],
            ['name' => 'teachers.edit', 'display_name' => 'O\'qituvchini tahrirlash', 'group' => 'teachers'],
            ['name' => 'teachers.delete', 'display_name' => 'O\'qituvchini o\'chirish', 'group' => 'teachers'],

            // Fanlar
            ['name' => 'subjects.view', 'display_name' => 'Fanlarni ko\'rish', 'group' => 'subjects'],
            ['name' => 'subjects.create', 'display_name' => 'Fan qo\'shish', 'group' => 'subjects'],
            ['name' => 'subjects.edit', 'display_name' => 'Fanni tahrirlash', 'group' => 'subjects'],
            ['name' => 'subjects.delete', 'display_name' => 'Fanni o\'chirish', 'group' => 'subjects'],

            // Guruhlar
            ['name' => 'groups.view', 'display_name' => 'Guruhlarni ko\'rish', 'group' => 'groups'],
            ['name' => 'groups.create', 'display_name' => 'Guruh qo\'shish', 'group' => 'groups'],
            ['name' => 'groups.edit', 'display_name' => 'Guruhni tahrirlash', 'group' => 'groups'],
            ['name' => 'groups.delete', 'display_name' => 'Guruhni o\'chirish', 'group' => 'groups'],

            // ✅ Ta'lim yo'nalishlari
            ['name' => 'directions.view', 'display_name' => 'Ta\'lim yo\'nalishlarini ko\'rish', 'group' => 'directions'],
            ['name' => 'directions.create', 'display_name' => 'Ta\'lim yo\'nalishi qo\'shish', 'group' => 'directions'],
            ['name' => 'directions.edit', 'display_name' => 'Ta\'lim yo\'nalishini tahrirlash', 'group' => 'directions'],
            ['name' => 'directions.delete', 'display_name' => 'Ta\'lim yo\'nalishini o\'chirish', 'group' => 'directions'],


            // O'quv yillari
            ['name' => 'academic-years.view', 'display_name' => 'O\'quv yillarini ko\'rish', 'group' => 'academic-years'],
            ['name' => 'academic-years.create', 'display_name' => 'O\'quv yili qo\'shish', 'group' => 'academic-years'],
            ['name' => 'academic-years.edit', 'display_name' => 'O\'quv yilini tahrirlash', 'group' => 'academic-years'],
            ['name' => 'academic-years.delete', 'display_name' => 'O\'quv yilini o\'chirish', 'group' => 'academic-years'],


            // Yuklamalar
            ['name' => 'workloads.view', 'display_name' => 'Yuklamalarni ko\'rish', 'group' => 'workloads'],
            ['name' => 'workloads.create', 'display_name' => 'Yuklama qo\'shish', 'group' => 'workloads'],
            ['name' => 'workloads.edit', 'display_name' => 'Yuklamani tahrirlash', 'group' => 'workloads'],
            ['name' => 'workloads.delete', 'display_name' => 'Yuklamani o\'chirish', 'group' => 'workloads'],
            ['name' => 'workloads.approve', 'display_name' => 'Yuklamani tasdiqlash', 'group' => 'workloads'],

            // Hisobotlar
            ['name' => 'reports.view', 'display_name' => 'Hisobotlarni ko\'rish', 'group' => 'reports'],
            ['name' => 'reports.export', 'display_name' => 'Hisobotlarni eksport qilish', 'group' => 'reports'],

            // Sozlamalar
            ['name' => 'settings.view', 'display_name' => 'Sozlamalarni ko\'rish', 'group' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Sozlamalarni tahrirlash', 'group' => 'settings'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // ========================================
        // ROLLARGA RUXSATLARNI BIRIKTIRISH
        // ========================================

        // ADMIN - Barcha huquqlar
        $admin = Role::where('name', 'admin')->first();
        $admin->permissions()->sync(Permission::all());

        // DEKAN
        $dekan = Role::where('name', 'dekan')->first();
        $dekan->permissions()->sync(Permission::whereIn('name', [
            'users.view',
            'faculties.view',
            'departments.view',
            'departments.edit',
            'teachers.view',
            'teachers.edit',
            'subjects.view',
            'groups.view',
            'directions.view',
            'directions.edit',
            'academic-years.view',
            'workloads.view',
            'workloads.approve',
            'reports.view',
            'reports.export'
        ])->get());

        // KAFEDRA MUDIRI
        $kafedra = Role::where('name', 'kafedra_mudiri')->first();
        $kafedra->permissions()->sync(Permission::whereIn('name', [
            'users.view',
            'departments.view',
            'teachers.view',
            'teachers.create',
            'teachers.edit',
            'subjects.view',
            'subjects.create',
            'subjects.edit',
            'subjects.delete',
            'groups.view',
            'directions.view',
            'academic-years.view',
            'workloads.view',
            'workloads.create',
            'workloads.edit',
            'workloads.approve',
            'reports.view'
        ])->get());

        // O'QITUVCHI
        $oqituvchi = Role::where('name', 'oqituvchi')->first();
        $oqituvchi->permissions()->sync(Permission::whereIn('name', [
            'workloads.view',
            'subjects.view',
            'groups.view',
            'directions.view',
            'academic-years.view',
        ])->get());

        // NAZORATCHI
        $nazoratchi = Role::where('name', 'nazoratchi')->first();
        $nazoratchi->permissions()->sync(Permission::whereIn('name', [
            'users.view',
            'faculties.view',
            'departments.view',
            'teachers.view',
            'subjects.view',
            'groups.view',
            'directions.view',
            'academic-years.view',
            'workloads.view',
            'reports.view',
            'reports.export'
        ])->get());

        $this->command->info('Rollar va ruxsatlar muvaffaqiyatli yaratildi!');
    }
}
