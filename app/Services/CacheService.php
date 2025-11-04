<?php

namespace App\Services;

use App\Models\Workload;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Cache;
use Exception;

class CacheService
{
    /**
     * Cache key prefixes
     */
    const WORKLOAD_PREFIX = 'workload:';
    const SUBJECT_STATS_PREFIX = 'subject_stats:';
    const TEACHER_STATS_PREFIX = 'teacher_stats:';
    const HOUR_VALIDATION_PREFIX = 'hour_validation:';

    /**
     * Cache TTL (seconds)
     */
    const CACHE_TTL = 3600; // 1 hour
    const SHORT_CACHE_TTL = 300; // 5 minutes

    /**
     * Workload cache
     */
    public static function getWorkload(int $workloadId)
    {
        return Cache::remember(
            self::WORKLOAD_PREFIX . $workloadId,
            self::CACHE_TTL,
            function () use ($workloadId) {
                return Workload::with([
                    'subject',
                    'teacher.user',
                    'teacher.department',
                    'direction',
                    'academicYear',
                ])->find($workloadId);
            }
        );
    }

    /**
     * Workload cacheni invalidate qilish
     */
    public static function invalidateWorkload(int $workloadId): void
    {
        Cache::forget(self::WORKLOAD_PREFIX . $workloadId);
    }

    /**
     * Subject statistics cache
     */
    public static function getSubjectStats(int $subjectId, int $academicYearId)
    {
        $key = self::SUBJECT_STATS_PREFIX . "{$subjectId}:{$academicYearId}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($subjectId, $academicYearId) {
            $remaining = WorkloadValidationService::getRemainingHours($subjectId, $academicYearId);
            $distributed = WorkloadValidationService::getDistributedHours($subjectId, $academicYearId);
            $percentage = WorkloadValidationService::getDistributionPercentage($subjectId, $academicYearId);

            return [
                'remaining' => $remaining,
                'distributed' => $distributed,
                'percentage' => $percentage,
            ];
        });
    }

    /**
     * Subject stats cacheni invalidate qilish
     */
    public static function invalidateSubjectStats(int $subjectId, int $academicYearId): void
    {
        Cache::forget(self::SUBJECT_STATS_PREFIX . "{$subjectId}:{$academicYearId}");
    }

    /**
     * Teacher statistics cache
     */
    public static function getTeacherStats(int $teacherId, int $academicYearId)
    {
        $key = self::TEACHER_STATS_PREFIX . "{$teacherId}:{$academicYearId}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($teacherId, $academicYearId) {
            $workloads = Workload::where('teacher_id', $teacherId)
                ->where('academic_year_id', $academicYearId)
                ->get();

            return [
                'total_workloads' => $workloads->count(),
                'confirmed_workloads' => $workloads->where('status', Workload::STATUS_CONFIRMED)->count(),
                'draft_workloads' => $workloads->where('status', Workload::STATUS_DRAFT)->count(),
                'pending_workloads' => $workloads->where('status', Workload::STATUS_PENDING)->count(),
                'total_hours' => $workloads->sum('total_hours'),
                'confirmed_hours' => $workloads->where('status', Workload::STATUS_CONFIRMED)->sum('total_hours'),
            ];
        });
    }

    /**
     * Teacher stats cacheni invalidate qilish
     */
    public static function invalidateTeacherStats(int $teacherId, int $academicYearId): void
    {
        Cache::forget(self::TEACHER_STATS_PREFIX . "{$teacherId}:{$academicYearId}");
    }

    /**
     * Hour validation cache
     */
    public static function getHourValidation(int $subjectId, int $academicYearId, array $requestData)
    {
        $hash = md5(json_encode($requestData));
        $key = self::HOUR_VALIDATION_PREFIX . "{$subjectId}:{$academicYearId}:{$hash}";

        return Cache::remember($key, self::SHORT_CACHE_TTL, function () use ($subjectId, $academicYearId, $requestData) {
            return WorkloadValidationService::validateHours($subjectId, $academicYearId, $requestData);
        });
    }

    /**
     * Barcha cacheni tozalash
     */
    public static function flushAll(): void
    {
        Cache::flush();
    }

    /**
     * Workload related cacheni tozalash
     */
    public static function flushWorkloadCache(): void
    {
        Cache::flush();
    }

    /**
     * Subject related cacheni tozalash
     */
    public static function flushSubjectCache(int $subjectId): void
    {
        // Subject bo'yicha barcha cached datani o'chirish
        $academicYears = AcademicYear::all();
        foreach ($academicYears as $year) {
            self::invalidateSubjectStats($subjectId, $year->id);
        }
    }

    /**
     * Teacher related cacheni tozalash
     */
    public static function flushTeacherCache(int $teacherId): void
    {
        // Teacher bo'yicha barcha cached datani o'chirish
        $academicYears = AcademicYear::all();
        foreach ($academicYears as $year) {
            self::invalidateTeacherStats($teacherId, $year->id);
        }
    }
}

?>