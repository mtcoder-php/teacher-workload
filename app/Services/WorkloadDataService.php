<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Direction;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Group;

class WorkloadDataService
{
    /**
     * Prepare data for workload create page
     */
    public function prepareCreateData(): array
    {
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Get authenticated user's department if they have one
        $userDepartment = null;
        if (auth()->user()->userable_type === 'App\\Models\\Teacher') {
            $teacher = auth()->user()->userable;
            $userDepartment = $teacher->department;
        }

        // Load teachers with proper relationships
        $teachers = Teacher::with([
            'user',
            'department'
        ])
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'department_id' => $teacher->department_id,
                    'full_name' => $teacher->user->name ?? ($teacher->first_name . ' ' . $teacher->last_name),
                    'position' => $teacher->position,
                    'user' => [
                        'id' => $teacher->user->id ?? null,
                        'name' => $teacher->user->name ?? null,
                    ],
                    'department' => $teacher->department ? [
                        'id' => $teacher->department->id,
                        'name' => $teacher->department->name,
                    ] : null,
                ];
            });

        // Load subjects with proper relationships
        $subjects = Subject::with('department')
            ->where('is_active', true)
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'department_id' => $subject->department_id,
                    'credit' => $subject->credit,

                    // Semester 1
                    'semester_1_lecture' => $subject->semester_1_lecture ?? 0,
                    'semester_1_practical' => $subject->semester_1_practical ?? 0,
                    'semester_1_laboratory' => $subject->semester_1_laboratory ?? 0,
                    'semester_1_seminar' => $subject->semester_1_seminar ?? 0,
                    'semester_1_practice' => $subject->semester_1_practice ?? 0,
                    'semester_1_exam' => $subject->semester_1_exam ?? 0,
                    'semester_1_test' => $subject->semester_1_test ?? 0,

                    // Semester 2
                    'semester_2_lecture' => $subject->semester_2_lecture ?? 0,
                    'semester_2_practical' => $subject->semester_2_practical ?? 0,
                    'semester_2_laboratory' => $subject->semester_2_laboratory ?? 0,
                    'semester_2_seminar' => $subject->semester_2_seminar ?? 0,
                    'semester_2_practice' => $subject->semester_2_practice ?? 0,
                    'semester_2_exam' => $subject->semester_2_exam ?? 0,
                    'semester_2_test' => $subject->semester_2_test ?? 0,

                    // Additional
                    'coursework_hours' => $subject->coursework_hours ?? 0,
                    'diploma_hours' => $subject->diploma_hours ?? 0,
                    'consultation_hours' => $subject->consultation_hours ?? 0,
                ];
            });

        // Load directions with groups
        $directions = Direction::with([
            'department',
            'groups' => function ($query) use ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear?->id)
                    ->where('is_active', true)
                    ->orderBy('course')
                    ->orderBy('name');
            }
        ])
            ->where('is_active', true)
            ->get()
            ->map(function ($direction) {
                return [
                    'id' => $direction->id,
                    'name' => $direction->name,
                    'code' => $direction->code,
                    'department_id' => $direction->department_id,
                    'groups' => $direction->groups->map(function ($group) {
                        return [
                            'id' => $group->id,
                            'name' => $group->name,
                            'code' => $group->code,
                            'course' => $group->course,
                            'student_count' => $group->student_count ?? 0,
                            'education_form' => $group->education_form,
                            'direction_id' => $group->direction_id,
                        ];
                    })->values(),
                ];
            });

        // Load departments
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'code' => $dept->code,
                ];
            });

        return [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'directions' => $directions,
            'departments' => $departments,
            'currentAcademicYear' => $currentAcademicYear ? [
                'id' => $currentAcademicYear->id,
                'name' => $currentAcademicYear->name,
                'start_date' => $currentAcademicYear->start_date,
                'end_date' => $currentAcademicYear->end_date,
            ] : null,
            'userDepartment' => $userDepartment ? [
                'id' => $userDepartment->id,
                'name' => $userDepartment->name,
            ] : null,
        ];
    }

    /**
     * Prepare data for workload edit page
     */
    public function prepareEditData($workload): array
    {
        $data = $this->prepareCreateData();

        // Add workload data
        $data['workload'] = [
            'id' => $workload->id,
            'teacher_id' => $workload->teacher_id,
            'subject_id' => $workload->subject_id,
            'department_id' => $workload->department_id,
            'is_potok' => $workload->is_potok,
            'potok_code' => $workload->potok_code,
            'group_ids' => $workload->groups->pluck('id')->toArray(),

            // Semester 1
            'semester_1_lecture' => $workload->semester_1_lecture,
            'semester_1_practical' => $workload->semester_1_practical,
            'semester_1_laboratory' => $workload->semester_1_laboratory,
            'semester_1_seminar' => $workload->semester_1_seminar,
            'semester_1_practice' => $workload->semester_1_practice,
            'semester_1_exam' => $workload->semester_1_exam,
            'semester_1_test' => $workload->semester_1_test,

            // Semester 2
            'semester_2_lecture' => $workload->semester_2_lecture,
            'semester_2_practical' => $workload->semester_2_practical,
            'semester_2_laboratory' => $workload->semester_2_laboratory,
            'semester_2_seminar' => $workload->semester_2_seminar,
            'semester_2_practice' => $workload->semester_2_practice,
            'semester_2_exam' => $workload->semester_2_exam,
            'semester_2_test' => $workload->semester_2_test,

            // Additional
            'coursework_hours' => $workload->coursework_hours,
            'diploma_hours' => $workload->diploma_hours,
            'consultation_hours' => $workload->consultation_hours,
            'rating' => $workload->rating,
            'total_students' => $workload->total_students,
            'notes' => $workload->notes,
        ];

        return $data;
    }
}
