<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Teacher;
use App\Models\Workload;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    // ─── Index sahifasi ───────────────────────────────────────────────────────

    public function index()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get(['id', 'name', 'is_active']);
        $activeYear    = $academicYears->firstWhere('is_active', true);

        $departments = Department::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $faculties   = Faculty::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $teachers    = Teacher::where('is_active', true)
            ->with('user:id,name')
            ->orderBy('id')
            ->get()
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->user?->name ?? '—']);

        return Inertia::render('Reports/Index', [
            'academicYears'    => $academicYears,
            'activeYearId'     => $activeYear?->id,
            'departments'      => $departments,
            'faculties'        => $faculties,
            'teachers'         => $teachers,
        ]);
    }

    // ─── Kafedra hisoboti ─────────────────────────────────────────────────────

    public function department(Request $request, Department $department)
    {
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        $academicYear = AcademicYear::find($academicYearId);

        // Kafedraning barcha o'qituvchilari va ularning yuklamalari
        $teachers = Teacher::where('department_id', $department->id)
            ->where('is_active', true)
            ->with([
                'user:id,name',
                'workloads' => fn($q) => $q
                    ->where('academic_year_id', $academicYearId)
                    ->where('status', 'confirmed'),
            ])
            ->get();

        $rows = $teachers->map(function ($teacher, $idx) {
            $workloads = $teacher->workloads;

            // Auditoriya soat = ma'ruza + amaliy + laboratoriya + seminar
            $auditoria = $workloads->sum(fn($w) =>
                $w->semester_1_lecture + $w->semester_1_practical +
                $w->semester_1_laboratory + $w->semester_1_seminar +
                $w->semester_2_lecture + $w->semester_2_practical +
                $w->semester_2_laboratory + $w->semester_2_seminar
            );
            $lecture   = $workloads->sum(fn($w) => $w->semester_1_lecture + $w->semester_2_lecture);
            $practical = $workloads->sum(fn($w) => $w->semester_1_practical + $w->semester_2_practical);
            $seminar   = $workloads->sum(fn($w) => $w->semester_1_seminar + $w->semester_2_seminar);
            $practice  = $workloads->sum(fn($w) => $w->semester_1_practice + $w->semester_2_practice);
            $coursework= $workloads->sum('coursework_hours');
            $diploma   = $workloads->sum('diploma_hours');
            $rating    = $workloads->sum('rating');
            $total     = $workloads->sum('total_hours');
            $stavka    = $teacher->employment_type === 'main_job' ? 1.0
                : ($teacher->employment_type === 'hourly' ? 0 : 0.5);

            return [
                'num'            => $idx + 1,
                'name'           => $teacher->user?->name ?? '—',
                'employment_type'=> $teacher->employmentTypeName,
                'stavka'         => $stavka,
                'position'       => $teacher->position ?? '',
                'degree'         => $teacher->academic_degree ?? '',
                'auditoria'      => round($auditoria, 1),
                'lecture'        => round($lecture, 1),
                'practical'      => round($practical, 1),
                'seminar'        => round($seminar, 1),
                'practice'       => round($practice, 1),
                'coursework'     => round($coursework, 1),
                'diploma'        => round($diploma, 1),
                'rating'         => round($rating, 1),
                'total'          => round($total, 1),
            ];
        });

        $totals = [
            'auditoria'  => round($rows->sum('auditoria'), 1),
            'lecture'    => round($rows->sum('lecture'), 1),
            'practical'  => round($rows->sum('practical'), 1),
            'seminar'    => round($rows->sum('seminar'), 1),
            'practice'   => round($rows->sum('practice'), 1),
            'coursework' => round($rows->sum('coursework'), 1),
            'diploma'    => round($rows->sum('diploma'), 1),
            'rating'     => round($rows->sum('rating'), 1),
            'total'      => round($rows->sum('total'), 1),
        ];

        $department->load('faculty');

        return Inertia::render('Reports/Department', [
            'department'   => [
                'id'      => $department->id,
                'name'    => $department->name,
                'faculty' => $department->faculty?->name,
            ],
            'academicYear'  => $academicYear,
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(['id', 'name', 'is_active']),
            'rows'          => $rows->values(),
            'totals'        => $totals,
        ]);
    }

    // ─── Fakultet hisoboti ────────────────────────────────────────────────────

    public function faculty(Request $request, Faculty $faculty)
    {
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        $academicYear = AcademicYear::find($academicYearId);

        // Fakultetning barcha yuklamalari — o'qituvchi, fan, guruhlar bilan
        $workloads = Workload::where('academic_year_id', $academicYearId)
            ->where('status', 'confirmed')
            ->whereHas('department', fn($q) => $q->where('faculty_id', $faculty->id))
            ->with([
                'teacher.user:id,name',
                'subject:id,name',
                'direction:id,name',
                'groups:id,name,course,student_count',
                'department:id,name',
            ])
            ->orderBy('teacher_id')
            ->get();

        // O'qituvchi bo'yicha guruhlash
        $grouped = $workloads->groupBy('teacher_id');

        $rows = [];
        foreach ($grouped as $teacherId => $teacherWorkloads) {
            $firstRow = true;
            $teacher  = $teacherWorkloads->first()->teacher;

            foreach ($teacherWorkloads as $w) {
                $groups     = $w->groups;
                $groupNames = $groups->pluck('name')->join(', ');
                $course     = $groups->first()?->course ?? '';
                $groupCount = $groups->count();
                $students   = $w->total_students ?? $groups->sum('student_count');

                // 1-semestr auditoriya
                $s1Auditoria = $w->semester_1_lecture + $w->semester_1_practical +
                    $w->semester_1_laboratory + $w->semester_1_seminar;
                $s1Total = $s1Auditoria + $w->semester_1_practice +
                    $w->semester_1_exam + $w->semester_1_test +
                    (($w->semester_2_lecture + $w->semester_2_practical +
                        $w->semester_2_laboratory + $w->semester_2_seminar) == 0
                        ? $w->coursework_hours + $w->rating : 0);

                // 2-semestr auditoriya
                $s2Auditoria = $w->semester_2_lecture + $w->semester_2_practical +
                    $w->semester_2_laboratory + $w->semester_2_seminar;
                $s2Total = $s2Auditoria + $w->semester_2_practice +
                    $w->semester_2_exam + $w->semester_2_test;

                $jami = $w->total_hours;

                $rows[] = [
                    'teacher_name'    => $firstRow ? ($teacher->user?->name ?? '—') : null,
                    'teacher_info'    => $firstRow ? ($teacher->academic_degree ?? '') : null,
                    'subject'         => $w->subject?->name ?? '—',
                    'direction'       => $w->direction?->name ?? '—',
                    'groups'          => $groupNames,
                    'course'          => $course,
                    'group_count'     => $groupCount,
                    'students'        => $students,
                    'is_potok'        => $w->is_potok ? 'P' : '',

                    // 1-semestr
                    's1_lecture'      => $w->semester_1_lecture ?: '',
                    's1_practical'    => $w->semester_1_practical ?: '',
                    's1_laboratory'   => $w->semester_1_laboratory ?: '',
                    's1_seminar'      => $w->semester_1_seminar ?: '',
                    's1_practice'     => $w->semester_1_practice ?: '',
                    's1_total'        => round($s1Auditoria + $w->semester_1_practice, 1) ?: '',

                    // 2-semestr
                    's2_lecture'      => $w->semester_2_lecture ?: '',
                    's2_practical'    => $w->semester_2_practical ?: '',
                    's2_laboratory'   => $w->semester_2_laboratory ?: '',
                    's2_seminar'      => $w->semester_2_seminar ?: '',
                    's2_practice'     => $w->semester_2_practice ?: '',
                    's2_total'        => round($s2Auditoria + $w->semester_2_practice, 1) ?: '',

                    // Qo'shimcha
                    'coursework'      => $w->coursework_hours ?: '',
                    'diploma'         => $w->diploma_hours ?: '',
                    'rating'          => $w->rating ?: '',

                    // Jami
                    'auditoria_total' => round(($s1Auditoria + $w->semester_1_practice) +
                        ($s2Auditoria + $w->semester_2_practice), 1),
                    'total'           => round($jami, 1),

                    'employment_type' => $firstRow ? $teacher->employment_type : null,
                ];
                $firstRow = false;
            }
        }

        $faculty->load('dean');

        return Inertia::render('Reports/Faculty', [
            'faculty'       => [
                'id'   => $faculty->id,
                'name' => $faculty->name,
                'dean' => $faculty->dean?->name,
            ],
            'academicYear'  => $academicYear,
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(['id', 'name', 'is_active']),
            'rows'          => $rows,
            'totals'        => [
                'total' => round($workloads->sum('total_hours'), 1),
            ],
        ]);
    }

    // ─── O'qituvchi hisoboti ──────────────────────────────────────────────────

    public function teacher(Request $request, Teacher $teacher)
    {
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        $academicYear = AcademicYear::find($academicYearId);

        $workloads = Workload::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $academicYearId)
            ->with([
                'subject:id,name,code',
                'groups:id,name,course,student_count',
                'direction:id,name',
            ])
            ->get();

        $teacher->load(['user:id,name', 'department:id,name']);

        $rows = $workloads->map(function ($w) {
            $groups = $w->groups;
            return [
                'subject'       => $w->subject?->name ?? '—',
                'direction'     => $w->direction?->name ?? '—',
                'groups'        => $groups->pluck('name')->join(', '),
                'course'        => $groups->first()?->course ?? '',
                'students'      => $w->total_students ?? $groups->sum('student_count'),
                'is_potok'      => $w->is_potok,

                // 1-semestr
                's1_lecture'    => $w->semester_1_lecture ?: 0,
                's1_practical'  => $w->semester_1_practical ?: 0,
                's1_laboratory' => $w->semester_1_laboratory ?: 0,
                's1_seminar'    => $w->semester_1_seminar ?: 0,
                's1_practice'   => $w->semester_1_practice ?: 0,

                // 2-semestr
                's2_lecture'    => $w->semester_2_lecture ?: 0,
                's2_practical'  => $w->semester_2_practical ?: 0,
                's2_laboratory' => $w->semester_2_laboratory ?: 0,
                's2_seminar'    => $w->semester_2_seminar ?: 0,
                's2_practice'   => $w->semester_2_practice ?: 0,

                // Qo'shimcha
                'coursework'    => $w->coursework_hours ?: 0,
                'diploma'       => $w->diploma_hours ?: 0,
                'consultation'  => $w->consultation_hours ?: 0,
                'rating'        => $w->rating ?: 0,
                'total'         => round($w->total_hours, 1),
                'status'        => $w->status,
            ];
        });

        $totals = [
            's1_lecture'    => round($rows->sum('s1_lecture'), 1),
            's1_practical'  => round($rows->sum('s1_practical'), 1),
            's1_laboratory' => round($rows->sum('s1_laboratory'), 1),
            's1_seminar'    => round($rows->sum('s1_seminar'), 1),
            's1_practice'   => round($rows->sum('s1_practice'), 1),
            's2_lecture'    => round($rows->sum('s2_lecture'), 1),
            's2_practical'  => round($rows->sum('s2_practical'), 1),
            's2_laboratory' => round($rows->sum('s2_laboratory'), 1),
            's2_seminar'    => round($rows->sum('s2_seminar'), 1),
            's2_practice'   => round($rows->sum('s2_practice'), 1),
            'coursework'    => round($rows->sum('coursework'), 1),
            'diploma'       => round($rows->sum('diploma'), 1),
            'consultation'  => round($rows->sum('consultation'), 1),
            'rating'        => round($rows->sum('rating'), 1),
            'total'         => round($rows->sum('total'), 1),
        ];

        $auditoria = round(
            $totals['s1_lecture'] + $totals['s1_practical'] + $totals['s1_laboratory'] + $totals['s1_seminar'] +
            $totals['s1_practice'] + $totals['s2_lecture'] + $totals['s2_practical'] +
            $totals['s2_laboratory'] + $totals['s2_seminar'] + $totals['s2_practice'], 1
        );

        return Inertia::render('Reports/Teacher', [
            'teacher' => [
                'id'         => $teacher->id,
                'name'       => $teacher->user?->name ?? '—',
                'position'   => $teacher->position ?? '',
                'degree'     => $teacher->academic_degree ?? '',
                'title'      => $teacher->academic_title ?? '',
                'department' => $teacher->department?->name ?? '',
                'employment' => $teacher->employmentTypeName,
            ],
            'academicYear'  => $academicYear,
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(['id', 'name', 'is_active']),
            'rows'          => $rows->values(),
            'totals'        => $totals,
            'auditoria'     => $auditoria,
        ]);
    }

    // ─── Excel export ─────────────────────────────────────────────────────────

    public function exportDepartment(Request $request, Department $department)
    {
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        return (new \App\Exports\DepartmentReportExport($department->id, $academicYearId))->download();
    }

    public function exportFaculty(Request $request, Faculty $faculty)
    {
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        return (new \App\Exports\FacultyReportExport($faculty->id, $academicYearId))->download();
    }

    public function exportTeacher(Request $request, Teacher $teacher)
    {
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        return (new \App\Exports\TeacherReportExport($teacher->id, $academicYearId))->download();
    }
}
