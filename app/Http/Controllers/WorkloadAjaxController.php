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
     *
     * MANTIQ:
     *  - Ma'ruza soatlari: global (barcha guruhlar bo'yicha) hisoblanadi
     *    chunki potok ma'ruzada barcha guruhlar qatnashadi
     *  - Amaliy/lab soatlari: faqat SHU GURUH bo'yicha hisoblanadi
     *    chunki har guruh o'z amaliy yuklamasiga ega
     */
    public function subjectDetails(Request $request, int $subjectId)
    {
        try {
            $subject        = Subject::findOrFail($subjectId);
            $academicYearId = $request->input('academic_year_id')
                ?? AcademicYear::where('is_active', true)->value('id');
            $groupIds       = array_filter((array) $request->input('group_ids', []));
            $excludeId      = $request->input('exclude_workload_id');

            $lectureFields  = ['semester_1_lecture', 'semester_2_lecture'];
            $practiceFields = [
                'semester_1_practical', 'semester_1_laboratory',
                'semester_1_seminar',   'semester_1_practice',
                'semester_1_exam',      'semester_1_test',
                'semester_2_practical', 'semester_2_laboratory',
                'semester_2_seminar',   'semester_2_practice',
                'semester_2_exam',      'semester_2_test',
                'coursework_hours',     'diploma_hours',
                'consultation_hours',
            ];

            // ── 1. Ma'ruza soatlari — GLOBAL (barcha guruhlar bo'yicha) ──────
            // Sabab: potok ma'ruzada barcha guruhlar qatnashadi,
            // shuning uchun ma'ruza 1 marta beriladi
            $lectureQuery = Workload::where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId);
            if ($excludeId) {
                $lectureQuery->where('id', '!=', (int) $excludeId);
            }
            $lectureSelect = array_map(fn($f) => "COALESCE(SUM({$f}), 0) as {$f}", $lectureFields);
            $lectureDist   = $lectureQuery->selectRaw(implode(', ', $lectureSelect))->first();

            // ── 2. Amaliy soatlar — FAQAT SHU GURUH bo'yicha ────────────────
            // Sabab: har guruh o'z amaliy yuklamasiga ega,
            // 1-guruhga berilgan amaliy 2-guruh limitini kamaytirmasligi kerak
            $practiceQuery = Workload::where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId)
                ->where(fn($q) => $q->where('is_potok', false)
                    ->orWhere('workload_type', '!=', 'lecture_only'));
            if ($excludeId) {
                $practiceQuery->where('id', '!=', (int) $excludeId);
            }
            if (!empty($groupIds)) {
                $practiceQuery->whereHas('groups', fn($g) => $g->whereIn('groups.id', $groupIds));
            }
            $practiceSelect = array_map(fn($f) => "COALESCE(SUM({$f}), 0) as {$f}", $practiceFields);
            $practiceDist   = $practiceQuery->selectRaw(implode(', ', $practiceSelect))->first();

            // ── Natijalarni birlashtirish ─────────────────────────────────────
            $maxHours = $remainingHours = [];
            foreach (self::FIELD_MAP as $field => $alias) {
                $max  = (float) ($subject->{$field} ?? 0);
                $used = in_array($field, $lectureFields)
                    ? (float) ($lectureDist->{$field}  ?? 0)
                    : (float) ($practiceDist->{$field} ?? 0);

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
