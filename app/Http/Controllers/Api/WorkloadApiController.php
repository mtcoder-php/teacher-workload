<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workload;
use App\Http\Resources\WorkloadResource;
use App\Services\WorkloadService;
use App\Services\WorkloadValidationService;
use App\Services\WorkloadStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkloadApiController extends Controller
{
    protected WorkloadService $workloadService;
    protected WorkloadValidationService $validationService;
    protected WorkloadStatisticsService $statisticsService;

    public function __construct(
        WorkloadService $workloadService,
        WorkloadValidationService $validationService,
        WorkloadStatisticsService $statisticsService
    ) {
        $this->workloadService = $workloadService;
        $this->validationService = $validationService;
        $this->statisticsService = $statisticsService;
    }

    /**
     * Yuklamalar ro'yxati
     */
    public function index(Request $request): JsonResponse
    {
        $query = Workload::with(['subject', 'teacher', 'group', 'direction', 'academicYear']);

        // Filtrlash
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('is_potok')) {
            $query->where('is_potok', $request->boolean('is_potok'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workloads = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => WorkloadResource::collection($workloads),
            'meta' => [
                'current_page' => $workloads->currentPage(),
                'last_page' => $workloads->lastPage(),
                'per_page' => $workloads->perPage(),
                'total' => $workloads->total(),
            ]
        ]);
    }

    /**
     * Bitta yuklama
     */
    public function show(Workload $workload): JsonResponse
    {
        $workload->load(['subject', 'teacher', 'group', 'direction', 'academicYear', 'department']);

        return response()->json([
            'success' => true,
            'data' => new WorkloadResource($workload)
        ]);
    }

    /**
     * Guruh statusini tekshirish
     */
    public function checkGroupStatus(Request $request): JsonResponse
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $status = $this->workloadService->checkGroupSubjectStatus(
            $request->group_id,
            $request->subject_id,
            $request->academic_year_id
        );

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }

    /**
     * Potok validatsiyasi
     */
    public function validatePotok(Request $request): JsonResponse
    {
        $request->validate([
            'group_ids' => 'required|array|min:2|max:10',
            'group_ids.*' => 'exists:groups,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $validation = $this->validationService->validateGroupsForPotok(
            $request->group_ids,
            $request->subject_id,
            $request->academic_year_id
        );

        return response()->json([
            'success' => $validation['valid'],
            'errors' => $validation['errors'] ?? [],
            'warnings' => $validation['warnings'] ?? [],
        ]);
    }

    /**
     * O'qituvchi statistikasi
     */
    public function teacherStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $stats = $this->statisticsService->getTeacherStatistics(
            $request->teacher_id,
            $request->academic_year_id
        );

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Kafedra statistikasi
     */
    public function departmentStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $stats = $this->statisticsService->getDepartmentStatistics(
            $request->department_id,
            $request->academic_year_id
        );

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Potok statistikasi
     */
    public function potokStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $stats = $this->statisticsService->getPotokStatistics(
            $request->academic_year_id,
            $request->department_id
        );

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
