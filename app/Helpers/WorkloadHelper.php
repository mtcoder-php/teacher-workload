<?php

namespace App\Helpers;

use App\Models\Workload;
use App\Models\Group;

class WorkloadHelper
{
    /**
     * Status rangini olish
     */
    public static function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'draft' => 'badge bg-secondary',
            'pending' => 'badge bg-warning',
            'confirmed' => 'badge bg-success',
            'completed' => 'badge bg-info',
            default => 'badge bg-light'
        };
    }

    /**
     * Soat turining nomini olish
     */
    public static function getHourTypeName(string $type): string
    {
        return match($type) {
            'lecture' => 'Ma\'ruza',
            'practical' => 'Amaliy',
            'laboratory' => 'Laboratoriya',
            'seminar' => 'Seminar',
            'practice' => 'Amaliyot',
            'exam' => 'Imtihon',
            'test' => 'Test',
            'coursework' => 'Kurs ishi',
            'diploma' => 'Diplom',
            'consultation' => 'Konsultatsiya',
            default => 'Noma\'lum'
        };
    }

    /**
     * Yuklama turini aniqlash
     */
    public static function determineWorkloadType(Workload $workload): string
    {
        if ($workload->is_potok) {
            return 'Potok (Ma\'ruza)';
        }

        $hasLecture = ($workload->semester_1_lecture + $workload->semester_2_lecture) > 0;
        $hasOthers = ($workload->semester_1_practical + $workload->semester_1_laboratory +
                $workload->semester_1_seminar + $workload->semester_2_practical +
                $workload->semester_2_laboratory + $workload->semester_2_seminar) > 0;

        if ($hasLecture && $hasOthers) {
            return 'To\'liq yuklama';
        } elseif ($hasLecture) {
            return 'Faqat ma\'ruza';
        } elseif ($hasOthers) {
            return 'Potok qoldig\'i';
        }

        return 'Boshqa';
    }

    /**
     * Guruh nomlarini formatlash
     */
    public static function formatGroupNames(array $groupIds): string
    {
        $groups = Group::whereIn('id', $groupIds)->pluck('name');

        if ($groups->count() <= 3) {
            return $groups->implode(', ');
        }

        $first = $groups->take(2)->implode(', ');
        $remaining = $groups->count() - 2;

        return "{$first} va yana {$remaining} ta";
    }

    /**
     * Soatlarni formatlash
     */
    public static function formatHours(float $hours): string
    {
        if ($hours == 0) {
            return '0';
        }

        if ($hours == (int)$hours) {
            return (string)(int)$hours;
        }

        return number_format($hours, 2);
    }

    /**
     * Reyting rangini olish
     */
    public static function getRatingColor(float $rating): string
    {
        if ($rating >= 100) {
            return 'success';
        } elseif ($rating >= 50) {
            return 'warning';
        }
        return 'danger';
    }

    /**
     * Yuklama foizini hisoblash
     */
    public static function calculateWorkloadPercentage(float $currentHours, float $maxHours = 900): float
    {
        if ($maxHours == 0) {
            return 0;
        }

        $percentage = ($currentHours / $maxHours) * 100;
        return min(100, round($percentage, 2));
    }

    /**
     * Guruhning ta'lim shakli nomini olish
     */
    public static function getEducationFormName(string $form): string
    {
        return match($form) {
            'full_time' => 'Kunduzgi',
            'evening' => 'Kechki',
            'correspondence' => 'Sirtqi',
            'distance' => 'Masofaviy',
            default => 'Noma\'lum'
        };
    }

    /**
     * Semestr nomini olish
     */
    public static function getSemesterName(int $semester): string
    {
        return $semester === 1 ? '1-semestr' : '2-semestr';
    }

    /**
     * Yuklama tasvirini yaratish
     */
    public static function generateWorkloadDescription(Workload $workload): string
    {
        $parts = [];

        if ($workload->is_potok) {
            $groupCount = count($workload->potok_group_ids ?? []);
            $parts[] = "Potok ({$groupCount} ta guruh)";
        } else {
            $parts[] = "Guruh: {$workload->group->name}";
        }

        $parts[] = "Fan: {$workload->subject->name}";
        $parts[] = "O'qituvchi: {$workload->teacher->full_name}";
        $parts[] = "Jami soat: " . self::formatHours($workload->total_hours);
        $parts[] = "Reyting: " . self::formatHours($workload->rating);

        return implode(' | ', $parts);
    }
}
