<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ReportPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permission yaratish
        $permission = Permission::firstOrCreate(
            ['name' => 'reports.export'],
            [
                'display_name' => 'Hisobotlarni eksport qilish',
                'description' => 'Excel va PDF formatda hisobotlarni yuklab olish imkoniyati',
            ]
        );

        // Admin rolga permission berish
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
            $this->command->info('Admin rolga reports.export permission berildi ✅');
        }

        // Dekan rolga ham berish (agar kerak bo'lsa)
        $dekanRole = Role::where('name', 'dekan')->first();
        if ($dekanRole) {
            $dekanRole->givePermissionTo($permission);
            $this->command->info('Dekan rolga reports.export permission berildi ✅');
        }

        // Kafedra mudiri rolga ham berish (agar kerak bo'lsa)
        $kafedraRole = Role::where('name', 'kafedra_mudiri')->first();
        if ($kafedraRole) {
            $kafedraRole->givePermissionTo($permission);
            $this->command->info('Kafedra mudiri rolga reports.export permission berildi ✅');
        }

        $this->command->info('Hisobot permissionlari muvaffaqiyatli yaratildi! 🎉');
    }
}