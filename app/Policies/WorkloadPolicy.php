<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workload;

class WorkloadPolicy
{
    /**
     * Barcha yuklamalarni ko'rish
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('workloads.view');
    }

    /**
     * Bitta yuklamani ko'rish
     */
    public function view(User $user, Workload $workload): bool
    {
        // Admin va O'quv bo'limi
        if ($user->hasPermission('workloads.view-all')) {
            return true;
        }

        // O'qituvchi - faqat o'ziniki
        if ($user->isTeacher() && $user->teacher) {
            if ($workload->teacher_id === $user->teacher->id) {
                return true;
            }

            // ✅ O'ZGARISH: Potokda ishtirok etsa
            if ($workload->is_potok) {
                $teacherGroupIds = $user->teacher->groups()->pluck('groups.id')->toArray();
                $workloadGroupIds = $workload->groups->pluck('id')->toArray();

                if (!empty(array_intersect($teacherGroupIds, $workloadGroupIds))) {
                    return true;
                }
            }
        }

        // Kafedra mudiri - o'z kafedrasidagi
        if ($user->isDepartmentHead() && $user->teacher) {
            return $workload->department_id === $user->teacher->department_id;
        }

        // Dekan - o'z fakultetidagi
        if ($user->isDean()) {
            return $workload->department->faculty_id === $user->faculty_id;
        }

        return false;
    }

    /**
     * Yuklama yaratish
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('workloads.create') &&
            ($user->isDepartmentHead() || $user->isAdmin());
    }

    /**
     * Yuklama tahrirlash
     */
    public function update(User $user, Workload $workload): bool
    {
        // Tasdiqlangan va tugallangan yuklamalarni faqat admin tahrirlaydi
        if (in_array($workload->status, ['confirmed', 'completed'])) {
            return $user->isAdmin();
        }

        // Kafedra mudiri - o'z kafedrasidagi draft va pending
        if ($user->isDepartmentHead() && $user->teacher) {
            return $workload->department_id === $user->teacher->department_id &&
                in_array($workload->status, ['draft', 'pending']);
        }

        return $user->hasPermission('workloads.edit');
    }

    /**
     * Yuklama o'chirish
     */
    public function delete(User $user, Workload $workload): bool
    {
        // Faqat draft holatdagi yuklamalarni o'chirish mumkin
        if ($workload->status !== 'draft') {
            return false;
        }

        // Kafedra mudiri - o'z kafedrasidagi
        if ($user->isDepartmentHead() && $user->teacher) {
            return $workload->department_id === $user->teacher->department_id;
        }

        return $user->hasPermission('workloads.delete');
    }

    /**
     * Yuklama tasdiqlash
     */
    public function approve(User $user, Workload $workload): bool
    {
        if (!in_array($workload->status, ['draft', 'pending'])) {
            return false;
        }

        if ($user->isDepartmentHead() && $user->teacher) {
            return $workload->department_id === $user->teacher->department_id;
        }

        return $user->hasPermission('workloads.approve');
    }

    /**
     * Yuklama rad etish
     */
    public function reject(User $user, Workload $workload): bool
    {
        if (!in_array($workload->status, ['draft', 'pending'])) {
            return false;
        }

        if ($user->isDepartmentHead() && $user->teacher) {
            return $workload->department_id === $user->teacher->department_id;
        }

        return $user->hasPermission('workloads.approve');
    }

    /**
     * Potok yaratish
     */
    public function createPotok(User $user): bool
    {
        return $user->isDepartmentHead() || $user->isAdmin();
    }

    /**
     * Potok o'chirish
     */
    public function deletePotok(User $user, Workload $workload): bool
    {
        if (!$workload->is_potok || $workload->status !== 'draft') {
            return false;
        }

        if ($user->isDepartmentHead() && $user->teacher) {
            return $workload->department_id === $user->teacher->department_id;
        }

        return $user->isAdmin();
    }
}
