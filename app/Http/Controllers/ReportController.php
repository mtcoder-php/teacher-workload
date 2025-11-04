<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Teacher;
use App\Models\Workload;
use App\Models\Semester;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Asosiy sahifa
     */
    public function index(): Response
    {


        // O'qituvchilarni olish
        $teachers = Teacher::with('user:id,name')
            ->where('is_active', true)
            ->get()
            ->map(function($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user ? $teacher->user->name : 'Noma\'lum',
                    'full_name' => $teacher->user ? $teacher->user->name : 'Noma\'lum',
                ];
            });

        // Kafedralarni olish
        $departments = Department::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Fakultetlarni olish
        $faculties = Faculty::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Permission tekshirish
        $canExport = false;
        if (auth()->check()) {
            $canExport = auth()->user()->can('reports.export');
        }

        return Inertia::render('Reports/Index', [
            'teachers' => $teachers,
            'departments' => $departments,
            'faculties' => $faculties,
            'canExport' => $canExport,
        ]);
    }

    /**
     * O'qituvchi bo'yicha hisobot
     */
    public function teacher(Request $request, Teacher $teacher): Response
    {
        $semesterId = $request->get('semester_id');

        $query = Workload::where('teacher_id', $teacher->id)
            ->with(['subject', 'group', 'semester']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        } else {
            $query->whereHas('semester', function ($q) {
                $q->where('is_current', true);
            });
        }

        $workloads = $query->get();

        // Statistika
        $stats = [
            'total_hours' => $workloads->sum('total_hours'),
            'lecture_hours' => $workloads->sum('lecture_hours'),
            'practical_hours' => $workloads->sum('practical_hours'),
            'seminar_hours' => $workloads->sum('seminar_hours'),
            'exam_hours' => $workloads->sum('exam_hours'),
            'total_subjects' => $workloads->unique('subject_id')->count(),
            'total_groups' => $workloads->unique('group_id')->count(),
        ];

        $teacher->load(['user', 'department']);

        return Inertia::render('Reports/Teacher', [
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->user->name ?? '',
                'full_name' => $teacher->user->name ?? '',
                'position' => $teacher->position ?? '',
                'department' => $teacher->department ? [
                    'id' => $teacher->department->id,
                    'name' => $teacher->department->name,
                ] : null,
            ],
            'workloads' => $workloads->map(function ($workload) {
                return [
                    'id' => $workload->id,
                    'subject' => [
                        'id' => $workload->subject->id,
                        'name' => $workload->subject->name,
                        'code' => $workload->subject->code ?? '',
                    ],
                    'group' => [
                        'id' => $workload->group->id,
                        'name' => $workload->group->name,
                    ],
                    'semester' => [
                        'id' => $workload->semester->id,
                        'name' => $workload->semester->name,
                    ],
                    'lecture_hours' => $workload->lecture_hours ?? 0,
                    'practical_hours' => $workload->practical_hours ?? 0,
                    'seminar_hours' => $workload->seminar_hours ?? 0,
                    'exam_hours' => $workload->exam_hours ?? 0,
                    'total_hours' => $workload->total_hours,
                ];
            })->values(),
            'stats' => $stats,
            'semesters' => Semester::select('id', 'name', 'is_current')->get(),
            'selectedSemester' => $semesterId,
            'canExport' => auth()->check() ? auth()->user()->can('reports.export') : false,
        ]);
    }

    /**
     * Kafedra bo'yicha hisobot
     */
    public function department(Request $request, Department $department): Response
    {
        $semesterId = $request->get('semester_id');

        $teachers = Teacher::where('department_id', $department->id)
            ->with(['user', 'workloads' => function ($query) use ($semesterId) {
                if ($semesterId) {
                    $query->where('semester_id', $semesterId);
                } else {
                    $query->whereHas('semester', function ($q) {
                        $q->where('is_current', true);
                    });
                }
            }])
            ->where('is_active', true)
            ->get();

        $teacherStats = $teachers->map(function ($teacher) {
            return [
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->user->name ?? '',
                    'full_name' => $teacher->user->name ?? '',
                    'position' => $teacher->position ?? '',
                ],
                'total_hours' => $teacher->workloads->sum('total_hours'),
                'workloads_count' => $teacher->workloads->count(),
                'lecture_hours' => $teacher->workloads->sum('lecture_hours'),
                'practical_hours' => $teacher->workloads->sum('practical_hours'),
                'seminar_hours' => $teacher->workloads->sum('seminar_hours'),
            ];
        })->values();

        $totalStats = [
            'teachers_count' => $teachers->count(),
            'total_hours' => $teacherStats->sum('total_hours'),
            'total_workloads' => $teacherStats->sum('workloads_count'),
            'average_hours' => $teachers->count() > 0
                ? round($teacherStats->sum('total_hours') / $teachers->count(), 2)
                : 0,
        ];

        // Load faculty and head
        $department->load(['faculty', 'head']);

        return Inertia::render('Reports/Department', [
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
                'head' => $department->head ? $department->head->name : '',
                'faculty' => $department->faculty ? [
                    'id' => $department->faculty->id,
                    'name' => $department->faculty->name,
                ] : null,
            ],
            'teacherStats' => $teacherStats,
            'totalStats' => $totalStats,
            'semesters' => Semester::select('id', 'name', 'is_current')->get(),
            'selectedSemester' => $semesterId,
            'canExport' => auth()->check() ? auth()->user()->can('reports.export') : false,
        ]);
    }

    /**
     * Fakultet bo'yicha hisobot
     */
    public function faculty(Request $request, Faculty $faculty): Response
    {
        $semesterId = $request->get('semester_id');

        $departments = Department::where('faculty_id', $faculty->id)
            ->with(['teachers' => function ($query) use ($semesterId) {
                $query->where('is_active', true)
                    ->with(['workloads' => function ($q) use ($semesterId) {
                        if ($semesterId) {
                            $q->where('semester_id', $semesterId);
                        } else {
                            $q->whereHas('semester', function ($sq) {
                                $sq->where('is_current', true);
                            });
                        }
                    }]);
            }, 'head'])
            ->where('is_active', true)
            ->get();

        $departmentStats = $departments->map(function ($department) {
            $totalHours = 0;
            $teachersCount = $department->teachers->count();

            foreach ($department->teachers as $teacher) {
                $totalHours += $teacher->workloads->sum('total_hours');
            }

            return [
                'department' => [
                    'id' => $department->id,
                    'name' => $department->name,
                    'head' => $department->head ? $department->head->name : '',
                ],
                'teachers_count' => $teachersCount,
                'total_hours' => $totalHours,
                'average_hours' => $teachersCount > 0 ? round($totalHours / $teachersCount, 2) : 0,
            ];
        })->values();

        $totalStats = [
            'departments_count' => $departments->count(),
            'teachers_count' => $departmentStats->sum('teachers_count'),
            'total_hours' => $departmentStats->sum('total_hours'),
        ];

        // Load dean
        $faculty->load('dean');

        return Inertia::render('Reports/Faculty', [
            'faculty' => [
                'id' => $faculty->id,
                'name' => $faculty->name,
                'dean' => $faculty->dean ? $faculty->dean->name : '',
                'code' => $faculty->code ?? '',
            ],
            'departmentStats' => $departmentStats,
            'totalStats' => $totalStats,
            'semesters' => Semester::select('id', 'name', 'is_current')->get(),
            'selectedSemester' => $semesterId,
            'canExport' => auth()->check() ? auth()->user()->can('reports.export') : false,
        ]);
    }

    /**
     * Excel export
     */
    public function exportExcel(Request $request)
    {
        // Route allaqachon middleware bilan himoyalangan

        $type = $request->get('type');
        $semesterId = $request->get('semester_id');

        try {
            switch ($type) {
                case 'teacher':
                    $teacherId = $request->get('teacher_id');
                    $teacher = Teacher::findOrFail($teacherId);
                    $fileName = 'oqituvchi_hisoboti_' . str_replace(' ', '_', $teacher->user->name) . '_' . date('Y-m-d') . '.xlsx';

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\TeacherReportExport($teacherId, $semesterId),
                        $fileName
                    );

                case 'department':
                    $departmentId = $request->get('department_id');
                    $department = Department::findOrFail($departmentId);
                    $fileName = 'kafedra_hisoboti_' . str_replace(' ', '_', $department->name) . '_' . date('Y-m-d') . '.xlsx';

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\DepartmentReportExport($departmentId, $semesterId),
                        $fileName
                    );

                case 'faculty':
                    $facultyId = $request->get('faculty_id');
                    $faculty = Faculty::findOrFail($facultyId);
                    $fileName = 'fakultet_hisoboti_' . str_replace(' ', '_', $faculty->name) . '_' . date('Y-m-d') . '.xlsx';

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\FacultyReportExport($facultyId, $semesterId),
                        $fileName
                    );

                default:
                    return back()->with('error', 'Noto\'g\'ri hisobot turi');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Excel yuklab olishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * PDF export
     */
   /**
 * PDF export
 */
public function exportPdf(Request $request)
{
    // Route allaqachon middleware bilan himoyalangan

    $type = $request->get('type');
    $semesterId = $request->get('semester_id');

    try {
        $pdf = null;
        $fileName = '';

        switch ($type) {
            case 'teacher':
                $teacherId = $request->get('teacher_id');
                $teacher = Teacher::with(['user', 'department'])->findOrFail($teacherId);

                $query = Workload::where('teacher_id', $teacherId)
                    ->with(['subject', 'group', 'semester']);

                if ($semesterId) {
                    $query->where('semester_id', $semesterId);
                } else {
                    $query->whereHas('semester', function ($q) {
                        $q->where('is_current', true);
                    });
                }

                $workloads = $query->get();

                $stats = [
                    'total_hours' => $workloads->sum('total_hours'),
                    'lecture_hours' => $workloads->sum('lecture_hours'),
                    'seminar_hours' => $workloads->sum('seminar_hours'),
                    'practical_hours' => $workloads->sum('practical_hours'),
                    'exam_hours' => $workloads->sum('exam_hours'),
                    'total_subjects' => $workloads->unique('subject_id')->count(),
                    'total_groups' => $workloads->unique('group_id')->count(),
                ];

                $semester = $semesterId ? Semester::find($semesterId)?->name : null;

                $pdf = \PDF::loadView('reports.teacher-pdf', compact('teacher', 'workloads', 'stats', 'semester'));
                $fileName = 'oqituvchi_hisoboti_' . str_replace(' ', '_', $teacher->user->name) . '_' . date('Y-m-d') . '.pdf';
                break;

            case 'department':
                $departmentId = $request->get('department_id');
                $department = Department::with(['faculty', 'head'])->findOrFail($departmentId);

                $teachers = Teacher::where('department_id', $departmentId)
                    ->with(['user', 'workloads' => function ($query) use ($semesterId) {
                        if ($semesterId) {
                            $query->where('semester_id', $semesterId);
                        } else {
                            $query->whereHas('semester', function ($q) {
                                $q->where('is_current', true);
                            });
                        }
                    }])
                    ->where('is_active', true)
                    ->get();

                $teacherStats = $teachers->map(function ($teacher) {
                    return [
                        'teacher' => [
                            'id' => $teacher->id,
                            'name' => $teacher->user->name ?? '',
                            'full_name' => $teacher->user->name ?? '',
                            'position' => $teacher->position ?? '',
                        ],
                        'total_hours' => $teacher->workloads->sum('total_hours'),
                        'workloads_count' => $teacher->workloads->count(),
                        'lecture_hours' => $teacher->workloads->sum('lecture_hours'),
                        'seminar_hours' => $teacher->workloads->sum('seminar_hours'),
                        'practical_hours' => $teacher->workloads->sum('practical_hours'),
                    ];
                })->values()->toArray();

                $totalStats = [
                    'teachers_count' => $teachers->count(),
                    'total_hours' => collect($teacherStats)->sum('total_hours'),
                    'total_workloads' => collect($teacherStats)->sum('workloads_count'),
                    'average_hours' => $teachers->count() > 0
                        ? round(collect($teacherStats)->sum('total_hours') / $teachers->count(), 2)
                        : 0,
                ];

                $semester = $semesterId ? Semester::find($semesterId)?->name : null;

                $pdf = \PDF::loadView('reports.department-pdf', compact('department', 'teacherStats', 'totalStats', 'semester'));
                $fileName = 'kafedra_hisoboti_' . str_replace(' ', '_', $department->name) . '_' . date('Y-m-d') . '.pdf';
                break;

            case 'faculty':
                $facultyId = $request->get('faculty_id');
                $faculty = Faculty::with('dean')->findOrFail($facultyId);

                $departments = Department::where('faculty_id', $facultyId)
                    ->with(['head', 'teachers' => function ($query) use ($semesterId) {
                        $query->where('is_active', true)
                            ->with(['workloads' => function ($q) use ($semesterId) {
                                if ($semesterId) {
                                    $q->where('semester_id', $semesterId);
                                } else {
                                    $q->whereHas('semester', function ($sq) {
                                        $sq->where('is_current', true);
                                    });
                                }
                            }]);
                    }])
                    ->where('is_active', true)
                    ->get();

                $departmentStats = $departments->map(function ($department) {
                    $totalHours = 0;
                    $teachersCount = $department->teachers->count();

                    foreach ($department->teachers as $teacher) {
                        $totalHours += $teacher->workloads->sum('total_hours');
                    }

                    return [
                        'department' => [
                            'id' => $department->id,
                            'name' => $department->name,
                            'head' => $department->head ? $department->head->name : '',
                        ],
                        'teachers_count' => $teachersCount,
                        'total_hours' => $totalHours,
                        'average_hours' => $teachersCount > 0 ? round($totalHours / $teachersCount, 2) : 0,
                    ];
                })->values()->toArray();

                $totalStats = [
                    'departments_count' => $departments->count(),
                    'teachers_count' => collect($departmentStats)->sum('teachers_count'),
                    'total_hours' => collect($departmentStats)->sum('total_hours'),
                ];

                $semester = $semesterId ? Semester::find($semesterId)?->name : null;

                $pdf = \PDF::loadView('reports.faculty-pdf', compact('faculty', 'departmentStats', 'totalStats', 'semester'));
                $fileName = 'fakultet_hisoboti_' . str_replace(' ', '_', $faculty->name) . '_' . date('Y-m-d') . '.pdf';
                break;

            default:
                return back()->with('error', 'Noto\'g\'ri hisobot turi');
        }

        return $pdf->download($fileName);

    } catch (\Exception $e) {
        \Log::error('PDF export error: ' . $e->getMessage());
        return back()->with('error', 'PDF yuklab olishda xatolik: ' . $e->getMessage());
    }
}
}
