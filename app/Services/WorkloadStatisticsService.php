<?php

namespace App\Services;

use App\Models\Workload;
use App\Models\Teacher;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class WorkloadStatisticsService
{
    /**
     * Kafedra statistikasi
     */
    public function getDepartmentStatistics(int $departmentId, int $academicYearId): array
    {
        $workloads = Workload::where('department_id', $departmentId)
            ->where('academic_year_id', $academicYearId)
            ->with(['teacher', 'subject', 'groups']) // ✅ O'ZGARISH: 'group' emas, 'groups'
            ->get();

        $potokWorkloads = $workloads->where('is_potok', true);
        $regularWorkloads = $workloads->where('is_potok', false);

        // ✅ O'ZGARISH: Potok guruhlar sonini hisoblash
        $potokGroupsCount = $potokWorkloads->pluck('groups')
            ->flatten()
            ->unique('id')
            ->count();

        return [
            'total_workloads' => $workloads->count(),
            'potok_workloads' => $potokWorkloads->count(),
            'regular_workloads' => $regularWorkloads->count(),

            'total_hours' => $workloads->sum('total_hours'),
            'total_rating' => $workloads->sum('rating'),

            'by_status' => [
                'draft' => $workloads->where('status', 'draft')->count(),
                'pending' => $workloads->where('status', 'pending')->count(),
                'confirmed' => $workloads->where('status', 'confirmed')->count(),
                'completed' => $workloads->where('status', 'completed')->count(),
            ],

            'potok_groups_count' => $potokGroupsCount,

            'teachers_count' => $workloads->unique('teacher_id')->count(),
            'subjects_count' => $workloads->unique('subject_id')->count(),
            'groups_count' => $workloads->pluck('groups')->flatten()->unique('id')->count(),
        ];
    }

    /**
     * O'qituvchi statistikasi - O'zgarishsiz
     */
    public function getTeacherStatistics(int $teacherId, int $academicYearId): array
    {
        $workloads = Workload::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYearId)
            ->with(['subject', 'groups']) // ✅ O'ZGARISH
            ->get();

        $confirmedWorkloads = $workloads->whereIn('status', ['confirmed', 'completed']);

        $hoursByType = [
            'lecture' => 0,
            'practical' => 0,
            'laboratory' => 0,
            'seminar' => 0,
            'practice' => 0,
            'exam' => 0,
            'test' => 0,
            'coursework' => 0,
            'diploma' => 0,
            'consultation' => 0,
        ];

        foreach ($confirmedWorkloads as $workload) {
            $hoursByType['lecture'] += $workload->semester_1_lecture + $workload->semester_2_lecture;
            $hoursByType['practical'] += $workload->semester_1_practical + $workload->semester_2_practical;
            $hoursByType['laboratory'] += $workload->semester_1_laboratory + $workload->semester_2_laboratory;
            $hoursByType['seminar'] += $workload->semester_1_seminar + $workload->semester_2_seminar;
            $hoursByType['practice'] += $workload->semester_1_practice + $workload->semester_2_practice;
            $hoursByType['exam'] += $workload->semester_1_exam + $workload->semester_2_exam;
            $hoursByType['test'] += $workload->semester_1_test + $workload->semester_2_test;
            $hoursByType['coursework'] += $workload->coursework_hours;
            $hoursByType['diploma'] += $workload->diploma_hours;
            $hoursByType['consultation'] += $workload->consultation_hours;
        }

        return [
            'total_workloads' => $workloads->count(),
            'potok_workloads' => $workloads->where('is_potok', true)->count(),
            'regular_workloads' => $workloads->where('is_potok', false)->count(),

            'total_hours' => $confirmedWorkloads->sum('total_hours'),
            'total_rating' => $confirmedWorkloads->sum('rating'),
            'total_students' => $confirmedWorkloads->sum('total_students'),

            'hours_by_type' => $hoursByType,

            'subjects_count' => $workloads->unique('subject_id')->count(),
            'groups_count' => $workloads->pluck('groups')->flatten()->unique('id')->count(),

            'by_status' => [
                'draft' => $workloads->where('status', 'draft')->count(),
                'pending' => $workloads->where('status', 'pending')->count(),
                'confirmed' => $workloads->where('status', 'confirmed')->count(),
                'completed' => $workloads->where('status', 'completed')->count(),
            ],
        ];
    }

    /**
     * Potok statistikasi
     */
    public function getPotokStatistics(int $academicYearId, ?int $departmentId = null): array
    {
        $query = Workload::where('is_potok', true)
            ->where('academic_year_id', $academicYearId);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $potoks = $query->with(['subject', 'teacher', 'groups'])->get(); // ✅ O'ZGARISH

        $groupedByCode = $potoks->groupBy('potok_code')->map(function($group) {
            $first = $group->first();
            return [
                'potok_code' => $first->potok_code,
                'subject_name' => $first->subject->name,
                'teacher_name' => $first->teacher->full_name,
                'group_count' => $first->groups->count(), // ✅ O'ZGARISH
                'total_students' => $first->total_students,
                'lecture_hours' => $first->semester_1_lecture + $first->semester_2_lecture,
                'rating' => $first->rating,
                'status' => $first->status,
            ];
        });

        return [
            'total_potoks' => $potoks->count(),
            'unique_potok_codes' => $potoks->unique('potok_code')->count(),
            'total_students' => $potoks->sum('total_students'),
            'total_rating' => $potoks->sum('rating'),
            'total_lecture_hours' => $potoks->sum(function($w) {
                return $w->semester_1_lecture + $w->semester_2_lecture;
            }),
            'potoks' => $groupedByCode->values(),
        ];
    }

    // Qolgan metodlar o'zgarishsiz
}
