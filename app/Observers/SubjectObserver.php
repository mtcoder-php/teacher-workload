<?php

namespace App\Observers;

use App\Models\Subject;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubjectObserver
{
    /**
     * Fan yaratilganda
     */
    public function creating(Subject $subject): void
    {
        // Kodni avtomatik uppercase qilish
        if ($subject->code) {
            $subject->code = strtoupper($subject->code);
        }

        // Agar patok yoqilmagan bo'lsa, default qiymat
        if (!$subject->can_be_potok) {
            $subject->min_groups_for_potok = 2;
        }

        // Qo'shimcha soatlarni 0 qilish
        $subject->coursework_hours = $subject->coursework_hours ?? 0;
        $subject->diploma_hours = $subject->diploma_hours ?? 0;
        $subject->consultation_hours = $subject->consultation_hours ?? 0;
    }

    /**
     * Fan yaratilganidan keyin
     */
    public function created(Subject $subject): void
    {
        Log::info('Yangi fan yaratildi', [
            'subject_id' => $subject->id,
            'name' => $subject->name,
            'code' => $subject->code,
            'department_id' => $subject->department_id,
            'user_id' => auth()->id(),
        ]);

        // Cache tozalash
        $this->clearCache($subject);
    }

    /**
     * Fan yangilanishidan oldin
     */
    public function updating(Subject $subject): void
    {
        // Kodni uppercase qilish
        if ($subject->isDirty('code')) {
            $subject->code = strtoupper($subject->code);
        }

        // Agar patok o'chirilsa
        if ($subject->isDirty('can_be_potok') && !$subject->can_be_potok) {
            $subject->min_groups_for_potok = 2;
        }
    }

    /**
     * Fan yangilanganidan keyin
     */
    public function updated(Subject $subject): void
    {
        // Muhim o'zgarishlarni log qilish
        $changes = $subject->getChanges();
        
        if (!empty($changes)) {
            Log::info('Fan yangilandi', [
                'subject_id' => $subject->id,
                'name' => $subject->name,
                'changes' => array_keys($changes),
                'user_id' => auth()->id(),
            ]);
        }

        // Agar soatlar o'zgartiriladigan bo'lsa, yuklama bilan bog'liq muammolar bo'lishi mumkin
        $hourFields = [
            'semester_1_lecture', 'semester_1_practical', 'semester_1_laboratory',
            'semester_1_seminar', 'semester_1_practice', 'semester_1_exam', 'semester_1_test',
            'semester_2_lecture', 'semester_2_practical', 'semester_2_laboratory',
            'semester_2_seminar', 'semester_2_practice', 'semester_2_exam', 'semester_2_test',
        ];

        $hoursChanged = false;
        foreach ($hourFields as $field) {
            if ($subject->wasChanged($field)) {
                $hoursChanged = true;
                break;
            }
        }

        if ($hoursChanged && $subject->workloads()->exists()) {
            Log::warning('Fan soatlari o\'zgartirildi, ammo yuklama mavjud', [
                'subject_id' => $subject->id,
                'name' => $subject->name,
                'workloads_count' => $subject->workloads()->count(),
            ]);
        }

        // Cache tozalash
        $this->clearCache($subject);
    }

    /**
     * Fan o'chirishdan oldin
     */
    public function deleting(Subject $subject): void
    {
        // Agar yuklamalar bo'lsa, o'chirishni to'xtatish
        if ($subject->workloads()->exists()) {
            Log::warning('Fanni o\'chirish amalga oshmadi - yuklamalar mavjud', [
                'subject_id' => $subject->id,
                'name' => $subject->name,
                'workloads_count' => $subject->workloads()->count(),
            ]);

            // Bu yerda exception tashlamaslik kerak, chunki
            // bu controller da hal qilinadi
        }
    }

    /**
     * Fan o'chirilganidan keyin
     */
    public function deleted(Subject $subject): void
    {
        Log::info('Fan o\'chirildi', [
            'subject_id' => $subject->id,
            'name' => $subject->name,
            'code' => $subject->code,
            'user_id' => auth()->id(),
        ]);

        // Cache tozalash
        $this->clearCache($subject);
    }

    /**
     * Fan tiklanganidan keyin (soft delete dan)
     */
    public function restored(Subject $subject): void
    {
        Log::info('Fan tiklandi', [
            'subject_id' => $subject->id,
            'name' => $subject->name,
            'user_id' => auth()->id(),
        ]);

        // Cache tozalash
        $this->clearCache($subject);
    }

    /**
     * Fan butunlay o'chirilganda (force delete)
     */
    public function forceDeleted(Subject $subject): void
    {
        Log::warning('Fan butunlay o\'chirildi', [
            'subject_id' => $subject->id,
            'name' => $subject->name,
            'user_id' => auth()->id(),
        ]);

        // Cache tozalash
        $this->clearCache($subject);
    }

    /**
     * Cache tozalash
     */
    private function clearCache(Subject $subject): void
    {
        // Fanlar ro'yxati cache
        Cache::forget("subjects_list");
        Cache::forget("subjects_list_active");
        Cache::forget("subjects_department_{$subject->department_id}");
        
        if ($subject->direction_id) {
            Cache::forget("subjects_direction_{$subject->direction_id}");
        }

        // Kurs darajasi bo'yicha cache
        Cache::forget("subjects_course_{$subject->course_level}");

        // Fan turi bo'yicha cache
        Cache::forget("subjects_type_{$subject->subject_type}");

        // Aniq fan cache
        Cache::forget("subject_{$subject->id}");
    }
}