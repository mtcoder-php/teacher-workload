<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class UpdatePermissionsGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groupMappings = [
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'faculties' => ['faculties.view', 'faculties.create', 'faculties.edit', 'faculties.delete'],
            'departments' => ['departments.view', 'departments.create', 'departments.edit', 'departments.delete'],
            'teachers' => ['teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete'],
            'subjects' => ['subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete'],
            'groups' => ['groups.view', 'groups.create', 'groups.edit', 'groups.delete'],
            'workloads' => ['workloads.view', 'workloads.create', 'workloads.edit', 'workloads.delete'],
            'reports' => ['reports.view', 'reports.export'],
            'settings' => ['settings.view', 'settings.edit'],
        ];

        foreach ($groupMappings as $group => $permissionNames) {
            Permission::whereIn('name', $permissionNames)
                ->update(['group' => $group]);
        }

        // Qolgan barchasi uchun default
        Permission::whereNull('group')
            ->update(['group' => 'general']);

        $this->command->info('Permissions groups successfully updated!');
    }
}