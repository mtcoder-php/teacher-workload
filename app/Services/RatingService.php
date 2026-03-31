<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Workload;

class RatingService
{
    /**
     * Guruhlar bo'yicha talabalar sonini hisoblash
     *
     * @param array $groupIds
     * @return int
     */
    public function calculateTotalStudents(array $groupIds): int
    {
        return Group::whereIn('id', $groupIds)->sum('student_count');
    }

    /**
     * Reytingni hisoblash
     *
     * Potoksiz: guruh talabalar soni / 2
     * Potokli (maruzachi): barcha guruhlar talabalar soni / 2
     * Potokli (seminarchi): faqat o'z guruhining talabalar soni / 2
     *
     * @param array $groupIds  — bu yuklamaning guruhlari
     * @return float
     */
    public function calculateRating(array $groupIds): float
    {
        $totalStudents = $this->calculateTotalStudents($groupIds);
        return round($totalStudents / 2, 2);
    }

    /**
     * Bu guruh + fan kombinatsiyasi uchun reyting allaqachon
     * boshqa yuklamaga berilganmi?
     *
     * Qoida: Bitta fan + bitta guruh = faqat 1 ta yuklamada has_rating = true
     *
     * @param int   $subjectId
     * @param array $groupIds        — yangi yuklamaning guruhlari
     * @param int   $academicYearId
     * @param int|null $excludeWorkloadId — tahrirlashda o'zini chiqarib tashlash
     * @return bool  — true = allaqachon berilgan (bloklash kerak)
     */
    public function isRatingAlreadyAssigned(
        int $subjectId,
        array $groupIds,
        int $academicYearId,
        ?int $excludeWorkloadId = null
    ): bool {
        $query = Workload::where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->where('has_rating', true)
            ->whereHas('groups', function ($q) use ($groupIds) {
                // Guruhlar kesishuvini tekshirish
                $q->whereIn('groups.id', $groupIds);
            });

        if ($excludeWorkloadId) {
            $query->where('id', '!=', $excludeWorkloadId);
        }

        return $query->exists();
    }

    /**
     * AJAX: Frontend uchun reyting holati
     * Bu guruh + fan uchun reyting berilganmi va qancha?
     *
     * @param int   $subjectId
     * @param array $groupIds
     * @param int   $academicYearId
     * @param int|null $excludeWorkloadId
     * @return array
     */
    public function getRatingStatus(
        int $subjectId,
        array $groupIds,
        int $academicYearId,
        ?int $excludeWorkloadId = null
    ): array {
        $totalStudents = $this->calculateTotalStudents($groupIds);
        $rating        = round($totalStudents / 2, 2);
        $isAssigned    = $this->isRatingAlreadyAssigned(
            $subjectId, $groupIds, $academicYearId, $excludeWorkloadId
        );

        // Allaqachon berilgan yuklamani topish (ma'lumot uchun)
        $assignedTo = null;
        if ($isAssigned) {
            $existing = Workload::where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId)
                ->where('has_rating', true)
                ->whereHas('groups', fn($q) => $q->whereIn('groups.id', $groupIds))
                ->when($excludeWorkloadId, fn($q) => $q->where('id', '!=', $excludeWorkloadId))
                ->with('teacher.user')
                ->first();

            $assignedTo = $existing?->teacher?->user?->name;
        }

        return [
            'total_students'  => $totalStudents,
            'rating'          => $rating,
            'is_assigned'     => $isAssigned,
            'assigned_to'     => $assignedTo,
            'can_assign'      => !$isAssigned,
        ];
    }
}
