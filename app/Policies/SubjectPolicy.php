<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    /**
     * Barcha fanlarni ko'rish
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('subjects.view');
    }

    /**
     * Bitta fanni ko'rish
     */
    public function view(User $user, Subject $subject): bool
    {
        return $user->hasPermission('subjects.view');
    }

    /**
     * Yangi fan yaratish
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('subjects.create');
    }

    /**
     * Fanni tahrirlash
     */
    public function update(User $user, Subject $subject): bool
    {
        // Asosiy ruxsat
        if (!$user->hasPermission('subjects.edit')) {
            return false;
        }

        // Agar foydalanuvchi kafedra mudiri bo'lsa, 
        // faqat o'z kafedrasining fanlarini tahrirlashi mumkin
        if ($user->hasRole('kafedra_mudiri')) {
            return $user->teacher && 
                   $user->teacher->department_id === $subject->department_id;
        }

        return true;
    }

    /**
     * Fanni o'chirish
     */
    public function delete(User $user, Subject $subject): bool
    {
        // Asosiy ruxsat
        if (!$user->hasPermission('subjects.delete')) {
            return false;
        }

        // Agar fanga yuklamalar biriktirilgan bo'lsa, o'chirish mumkin emas
        if ($subject->workloads()->exists()) {
            return false;
        }

        // Kafedra mudiri faqat o'z kafedrasining fanlarini o'chirishi mumkin
        if ($user->hasRole('kafedra_mudiri')) {
            return $user->teacher && 
                   $user->teacher->department_id === $subject->department_id;
        }

        return true;
    }

    /**
     * Fanni tiklash (soft delete dan)
     */
    public function restore(User $user, Subject $subject): bool
    {
        return $user->hasPermission('subjects.delete');
    }

    /**
     * Fanni butunlay o'chirish (force delete)
     */
    public function forceDelete(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Fanni faollashtirish/faolsizlantirish
     */
    public function toggleActive(User $user, Subject $subject): bool
    {
        return $user->hasPermission('subjects.edit');
    }

    /**
     * Fan soatlarini ko'rish
     */
    public function viewHours(User $user, Subject $subject): bool
    {
        return $user->hasPermission('subjects.view') ||
               $user->hasPermission('workloads.view');
    }

    /**
     * Fan statistikasini ko'rish
     */
    public function viewStatistics(User $user, Subject $subject): bool
    {
        return $user->hasPermission('subjects.view') ||
               $user->hasAnyRole(['dekan', 'kafedra_mudiri', 'o\'quv_ishlari']);
    }
}