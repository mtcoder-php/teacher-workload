<?php

namespace App\Services;

use App\Models\Workload;
use App\Models\Teacher;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class WorkloadReportService
{
    /**
     * O'qituvchi uchun to'liq hisobot
     */
    public function generateTeacherReport(int $teacherId, int $academicYearId): array
    {
        $teacher = Teacher::with('department')->findOrFail($teacherId);

        $workloads = Workload::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYearId)
            ->with(['subject', 'groups', 'direction']) // ✅ O'ZGARISH
            ->get();

        $confirmedWorkloads = $workloads->whereIn('status', ['confirmed', 'completed']);

        $report = [
            'teacher' => [
                'id' => $teacher->id,
                'full_name' => $teacher->full_name,
                'department' => $teacher->department->name ?? '',
                'position' => $teacher->position ?? '',
            ],

            'summary' => [
                'total_workloads' => $workloads->count(),
                'confirmed_workloads' => $confirmedWorkloads->count(),
                'potok_workloads' => $workloads->where('is_potok', true)->count(),
                'regular_workloads' => $workloads->where('is_potok', false)->count(),
                'total_hours' => $confirmedWorkloads->sum('total_hours'),
                'total_rating' => $confirmedWorkloads->sum('rating'),
                'total_students' => $confirmedWorkloads->sum('total_students'),
            ],

            'hours_breakdown' => $this->calculateHoursBreakdown($confirmedWorkloads),

            'workloads_detail' => $confirmedWorkloads->map(function($workload) {
                return [
                    'id' => $workload->id,
                    'subject_name' => $workload->subject->name,
                    'subject_code' => $workload->subject->code,
                    'groups' => $workload->groups->pluck('name')->join(', '), // ✅ O'ZGARISH
                    'is_potok' => $workload->is_potok,
                    'potok_code' => $workload->potok_code,
                    'potok_groups' => $workload->is_potok ? $workload->groups->count() : null,
                    'total_hours' => $workload->total_hours,
                    'rating' => $workload->rating,
                    'students' => $workload->total_students,
                    'status' => $workload->status_label,
                ];
            })->values(),
        ];

        return $report;
    }

    // Qolgan metodlar ham xuddi shunday o'zgaradi
    // 'group' -> 'groups'
}
