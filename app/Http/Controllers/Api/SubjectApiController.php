<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubjectApiController extends Controller
{
    public function __construct(
        private SubjectService $subjectService
    ) {}

    /**
     * Fanlar ro'yxati (API)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::with(['department', 'direction']);

        // Filtrlar
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        if ($request->filled('course_level')) {
            $query->where('course_level', $request->course_level);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('can_be_potok')) {
            $query->where('can_be_potok', $request->boolean('can_be_potok'));
        }

        // Qidiruv
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $subjects = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => SubjectResource::collection($subjects),
            'meta' => [
                'current_page' => $subjects->currentPage(),
                'last_page' => $subjects->lastPage(),
                'per_page' => $subjects->perPage(),
                'total' => $subjects->total(),
            ],
        ]);
    }

    /**
     * Bitta fanni ko'rish (API)
     */
    public function show(Subject $subject, Request $request): JsonResponse
    {
        $subject->load(['department', 'direction']);

        $data = [
            'subject' => new SubjectResource($subject),
        ];

        // Agar academic_year_id berilgan bo'lsa, statistika qo'shish
        if ($request->filled('academic_year_id')) {
            $academicYearId = $request->academic_year_id;
            $data['statistics'] = $this->subjectService->getStatistics($subject, $academicYearId);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Fan soatlarini olish (API)
     */
    public function hours(Subject $subject, Request $request): JsonResponse
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:1,2',
        ]);

        $academicYearId = $request->academic_year_id;
        $semester = $request->semester;

        $semesterHours = $subject->getSemesterHours($semester);
        $distributed = $subject->getDistributedHours($academicYearId, $semester);
        $remaining = $subject->getRemainingHours($academicYearId, $semester);
        $percentage = $subject->getDistributionPercentage($academicYearId, $semester);

        return response()->json([
            'success' => true,
            'data' => [
                'semester' => $semester,
                'planned' => $semesterHours,
                'distributed' => $distributed,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'is_fully_distributed' => $subject->isFullyDistributed($academicYearId, $semester),
            ],
        ]);
    }

    /**
     * Patok imkoniyatini tekshirish (API)
     */
    public function checkPotok(Subject $subject, Request $request): JsonResponse
    {
        $request->validate([
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',
        ]);

        $result = $this->subjectService->checkPotokPossibility(
            $subject,
            $request->group_ids
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Kafedra fanlari tahlili (API)
     */
    public function departmentAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $analysis = $this->subjectService->analyzeDepartmentSubjects(
            $request->department_id,
            $request->academic_year_id
        );

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Yo'nalish bo'yicha fanlar (API)
     */
    public function byDirection(int $directionId): JsonResponse
    {
        $subjects = Subject::where('direction_id', $directionId)
            ->where('is_active', true)
            ->with(['department'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubjectResource::collection($subjects),
        ]);
    }

    /**
     * Kafedra bo'yicha fanlar (API)
     */
    public function byDepartment(int $departmentId): JsonResponse
    {
        $subjects = Subject::where('department_id', $departmentId)
            ->where('is_active', true)
            ->with(['direction'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubjectResource::collection($subjects),
        ]);
    }

    /**
     * Kurs darajasi bo'yicha fanlar (API)
     */
    public function byCourseLevel(int $courseLevel): JsonResponse
    {
        $subjects = Subject::where('course_level', $courseLevel)
            ->where('is_active', true)
            ->with(['department', 'direction'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubjectResource::collection($subjects),
        ]);
    }

    /**
     * Fan statistikasi (API)
     */
    public function statistics(Subject $subject, Request $request): JsonResponse
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $statistics = $this->subjectService->getStatistics(
            $subject,
            $request->academic_year_id
        );

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Fan to'liq ma'lumotlari (API)
     */
    public function fullData(Subject $subject, Request $request): JsonResponse
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $data = $this->subjectService->getFullSubjectData(
            $subject,
            $request->academic_year_id
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}