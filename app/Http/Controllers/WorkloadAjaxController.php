<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Workload;
use App\Services\WorkloadValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkloadAjaxController extends Controller
{
    protected WorkloadValidationService $validationService;

    public function __construct(WorkloadValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    // ─── Soat maydonlari map ──────────────────────────────────────────────────
    private const FIELD_MAP = [
        'semester_1_lecture'    => 's1_lec',
        'semester_1_practical'  => 's1_pra',
        'semester_1_laboratory' => 's1_lab',
        'semester_1_seminar'    => 's1_sem',
        'semester_1_practice'   => 's1_prc',
        'semester_1_exam'       => 's1_ex',
        'semester_1_test'       => 's1_tst',
        'semester_2_lecture'    => 's2_lec',
        'semester_2_practical'  => 's2_pra',
        'semester_2_laboratory' => 's2_lab',
        'semester_2_seminar'    => 's2_sem',
        'semester_2_practice'   => 's2_prc',
        'semester_2_exam'       => 's2_ex',
        'semester_2_test'       => 's2_tst',
        'coursework_hours'      => 'cw',
        'diploma_hours'         => 'dip',
        'consultation_hours'    => 'con',
    ];

    /**
     * Fan soatlari va qolgan limitini hisoblash
     * GET /workloads/ajax/subject/{subjectId}/details
     *   ?academic_year_id=1
     *   &group_ids[]=5&group_ids[]=6
     *   &exclude_workload_id=14   ← edit uchun
     */
    public function subjectDetails(Request $request, int $subjectId)
    {
        try {
            $subject        = Subject::findOrFail($subjectId);
            $academicYearId = $request->input('academic_year_id')
                ?? AcademicYear::where('is_active', true)->value('id');

            $query = Workload::where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId);

            // Edit: o'z soatlarini limitdan chiqarish
            if ($excludeId = $request->input('exclude_workload_id')) {
                $query->where('id', '!=', (int) $excludeId);
            }

            // MUHIM: group_ids filtri YO'Q!
            // Fan soatlarini hisoblashda BARCHA guruhlar uchun taqsimlangan
            // soatlar hisobga olinadi. Masalan, fan soati = 100, 1-guruhga
            // 60 soat berilgan bo'lsa, 2-guruh uchun qolgan = 40.

            // SELECT raw alias lar
            $selectParts = array_map(
                fn($field, $alias) => "COALESCE(SUM({$field}), 0) as {$alias}",
                array_keys(self::FIELD_MAP),
                array_values(self::FIELD_MAP)
            );

            $dist = $query->selectRaw(implode(', ', $selectParts))->first();

            $maxHours = $remainingHours = [];
            foreach (self::FIELD_MAP as $field => $alias) {
                $max                    = (float) ($subject->{$field} ?? 0);
                $used                   = (float) ($dist->{$alias}   ?? 0);
                $maxHours[$field]       = $max;
                $remainingHours[$field] = max(0, $max - $used);
            }

            $isFullyUsed = collect($maxHours)->some(fn($v) => $v > 0)
                && collect($remainingHours)->every(fn($v) => $v == 0);

            return response()->json([
                'success'         => true,
                'max_hours'       => $maxHours,
                'remaining_hours' => $remainingHours,
                'is_fully_used'   => $isFullyUsed,
            ]);

        } catch (\Exception $e) {
            Log::error('subjectDetails: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Guruhlar holati — bu guruh + fan allaqachon mavjudmi?
     * POST /workloads/ajax/check-groups-status
     */
    public function checkGroupsStatus(Request $request)
    {
        try {
            $groupIds       = $request->input('group_ids', []);
            $subjectId      = $request->input('subject_id');
            $academicYearId = $request->input('academic_year_id');

            if (empty($groupIds) || !$subjectId || !$academicYearId) {
                return response()->json(
                    ['success' => false, 'message' => 'Parametrlar to\'liq emas'],
                    400
                );
            }

            $results = $this->validationService->checkMultipleGroupsStatus(
                $groupIds, $subjectId, $academicYearId
            );

            return response()->json(['success' => true, 'data' => $results]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik'], 500);
        }
    }

    /**
     * Reyting holati — bu guruhlar uchun reyting berilganmi?
     * GET /workloads/ajax/rating-status
     *   ?subject_id=X&group_ids[]=1&group_ids[]=2
     */
    public function ratingStatus(Request $request)
    {
        $subjectId      = $request->input('subject_id');
        $groupIds       = $request->input('group_ids', []);
        $academicYearId = $request->input('academic_year_id')
            ?? AcademicYear::where('is_active', true)->value('id');

        if (!$subjectId || empty($groupIds)) {
            return response()->json([
                'success'     => true,
                'is_assigned' => false,
                'assigned_to' => null,
            ]);
        }

        $assigned = Workload::where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->where('has_rating', true)
            ->whereHas('groups', fn($q) => $q->whereIn('groups.id', $groupIds))
            ->with('teacher.user')
            ->first();

        return response()->json([
            'success'     => true,
            'is_assigned' => $assigned !== null,
            'assigned_to' => $assigned?->teacher?->user?->name,
        ]);
    }
}
