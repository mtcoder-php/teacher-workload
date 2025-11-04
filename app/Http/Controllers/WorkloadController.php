<?php

namespace App\Http\Controllers;

use App\Models\Workload;
use App\Models\Subject;
use App\Http\Requests\StoreWorkloadRequest;
use App\Services\WorkloadService;
use App\Services\WorkloadValidationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WorkloadController extends Controller
{
    protected $workloadService;
    protected $validationService;

    public function __construct(
        WorkloadService $workloadService,
        WorkloadValidationService $validationService
    ) {
        $this->workloadService = $workloadService;
        $this->validationService = $validationService;
    }

    /**
     * Display workloads list
     */
    public function index(Request $request)
    {
        $query = Workload::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('notes', 'like', "%{$request->search}%");
            });
        }

        // Filters
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->academic_year_id) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->direction_id) {
            $query->where('direction_id', $request->direction_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->is_potok !== null) {
            $query->where('is_potok', $request->is_potok == '1');
        }

        $workloads = $query->with(['groups'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Filter options from database
        $departments = DB::table('departments')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $teachers = DB::table('teachers')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->where('teachers.is_active', true)
            ->orderBy('users.name')
            ->select('teachers.id', 'users.name as full_name')
            ->get();

        $subjects = DB::table('subjects')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $academicYears = DB::table('academic_years')
            ->orderBy('start_date', 'desc')
            ->get(['id', 'name']);

        $directions = DB::table('directions')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Workloads/Index', [
            'workloads' => $workloads,
            'filters' => $request->only(['search', 'department_id', 'teacher_id', 'subject_id', 'academic_year_id', 'direction_id', 'status', 'is_potok']),
            'departments' => $departments,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'academicYears' => $academicYears,
            'directions' => $directions,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $currentAcademicYear = DB::table('academic_years')
            ->where('is_active', true)
            ->first();

        // Departments
        $departments = DB::table('departments')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        // Teachers
        $teachers = DB::table('teachers')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->where('teachers.is_active', true)
            ->select('teachers.id', 'teachers.department_id', 'users.name as full_name', 'teachers.position')
            ->orderBy('users.name')
            ->get();

        // Subjects with hours
        $subjects = DB::table('subjects')
            ->where('is_active', true)
            ->select(
                'id', 'name', 'code', 'department_id',
                'semester_1_lecture', 'semester_1_practical', 'semester_1_laboratory',
                'semester_1_seminar', 'semester_1_practice', 'semester_1_exam', 'semester_1_test',
                'semester_2_lecture', 'semester_2_practical', 'semester_2_laboratory',
                'semester_2_seminar', 'semester_2_practice', 'semester_2_exam', 'semester_2_test',
                'coursework_hours', 'diploma_hours', 'consultation_hours'
            )
            ->orderBy('name')
            ->get();

        // Directions
        $directions = DB::table('directions')
            ->where('is_active', true)
            ->select('id', 'name', 'code', 'department_id')
            ->orderBy('name')
            ->get();

        // Groups
        $groups = DB::table('groups')
            ->where('is_active', true)
            ->when($currentAcademicYear, function ($query) use ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id);
            })
            ->select('id', 'name', 'code', 'direction_id', 'course', 'student_count')
            ->orderBy('name')
            ->get();

        // Academic Years
        $academicYears = DB::table('academic_years')
            ->orderBy('start_date', 'desc')
            ->get(['id', 'name', 'is_active']);

        return Inertia::render('Workloads/Create', [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'directions' => $directions,
            'departments' => $departments,
            'groups' => $groups,
            'academicYears' => $academicYears,
            'currentAcademicYear' => $currentAcademicYear ? [
                'id' => $currentAcademicYear->id,
                'name' => $currentAcademicYear->name,
            ] : null,
        ]);
    }

    /**
     * Store workload
     */
    public function store(StoreWorkloadRequest $request)
    {

        try {
            $workload = $this->workloadService->createWorkload($request->validated());

            return redirect()->route('workloads.index')
                ->with('success', 'Yuklama muvaffaqiyatli yaratildi!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display workload
     */
    public function show(Workload $workload)
    {
        $workload->load(['teacher.user', 'subject', 'department', 'groups', 'academicYear', 'direction']);

        // Agar potok bo'lsa, barcha guruhlarni yuklash
        $potokGroups = [];
        if ($workload->is_potok) {
            $potokGroups = $workload->groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'code' => $group->code,
                    'student_count' => $group->student_count,
                ];
            });
        }

        // Statistika
        $stats = [
            'semester_1_total' => $workload->semester_1_lecture + $workload->semester_1_practical +
                $workload->semester_1_laboratory + $workload->semester_1_seminar,
            'semester_2_total' => $workload->semester_2_lecture + $workload->semester_2_practical +
                $workload->semester_2_laboratory + $workload->semester_2_seminar,
        ];
        $stats['grand_total'] = $stats['semester_1_total'] + $stats['semester_2_total'];

        // Permissions
        $canEdit = $workload->status === 'draft';
        $canDelete = $workload->status === 'draft';

        return Inertia::render('Workloads/Show', [
            'workload' => $workload,
            'potokGroups' => $potokGroups,
            'stats' => $stats,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(Workload $workload)
    {
        $workload->load(['groups', 'teacher', 'subject']);

        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        $teachers = Teacher::with(['user', 'department'])->whereHas('user')->get();
        $subjects = Subject::where('is_active', true)->get();
        $directions = Direction::with(['groups'])->where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return Inertia::render('Workloads/Edit', [
            'workload' => $workload,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'directions' => $directions,
            'departments' => $departments,
            'currentAcademicYear' => $currentAcademicYear,
        ]);
    }

    /**
     * Update workload
     */
    public function update(Request $request, Workload $workload)
    {
        try {
            $this->workloadService->updateWorkload($workload, $request->all());
            return redirect()->route('workloads.index')->with('success', 'Yuklama yangilandi!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete workload
     */
    public function destroy(Workload $workload)
    {
        try {
            $this->workloadService->deleteWorkload($workload);
            return redirect()->route('workloads.index')->with('success', 'Yuklama o\'chirildi!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * AJAX: Get subject details
     */
    public function getSubjectDetails($subjectId)
    {
        try {
            // withTrashed() - o'chirilgan subject'larni ham olish
            $subject = Subject::withTrashed()->findOrFail($subjectId);

            return response()->json([
                'success' => true,
                'data' => [
                    'semester_1_lecture' => $subject->semester_1_lecture ?? 0,
                    'semester_1_practical' => $subject->semester_1_practical ?? 0,
                    'semester_1_laboratory' => $subject->semester_1_laboratory ?? 0,
                    'semester_1_seminar' => $subject->semester_1_seminar ?? 0,
                    'semester_1_practice' => $subject->semester_1_practice ?? 0,
                    'semester_1_exam' => $subject->semester_1_exam ?? 0,
                    'semester_1_test' => $subject->semester_1_test ?? 0,

                    'semester_2_lecture' => $subject->semester_2_lecture ?? 0,
                    'semester_2_practical' => $subject->semester_2_practical ?? 0,
                    'semester_2_laboratory' => $subject->semester_2_laboratory ?? 0,
                    'semester_2_seminar' => $subject->semester_2_seminar ?? 0,
                    'semester_2_practice' => $subject->semester_2_practice ?? 0,
                    'semester_2_exam' => $subject->semester_2_exam ?? 0,
                    'semester_2_test' => $subject->semester_2_test ?? 0,

                    'coursework_hours' => $subject->coursework_hours ?? 0,
                    'diploma_hours' => $subject->diploma_hours ?? 0,
                    'consultation_hours' => $subject->consultation_hours ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Subject details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fan topilmadi: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * AJAX: Check groups status
     */
    public function checkGroupsStatus(Request $request)
    {
        try {
            $groupIds = $request->input('group_ids', []);
            $subjectId = $request->input('subject_id');
            $academicYearId = $request->input('academic_year_id');

            if (empty($groupIds) || !$subjectId || !$academicYearId) {
                return response()->json(['success' => false, 'message' => 'Parametrlar to\'liq emas'], 400);
            }

            $results = $this->validationService->checkMultipleGroupsStatus(
                $groupIds,
                $subjectId,
                $academicYearId
            );

            return response()->json(['success' => true, 'data' => $results]);

        } catch (\Exception $e) {
            Log::error('Check groups error:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Xatolik'], 500);
        }
    }

    /**
     * Create potok remainder (amaliy soatlar uchun)
     */
    public function createRemainder(Request $request, Workload $potok)
    {
        try {
            if (!$potok->is_potok) {
                return back()->withErrors(['error' => 'Bu yuklama potok emas']);
            }

            $remainder = $this->workloadService->createPotokRemainder(
                $request->all(),
                $potok->id
            );

            return redirect()->route('workloads.show', $remainder)
                ->with('success', 'Potok qoldig\'i yaratildi!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
