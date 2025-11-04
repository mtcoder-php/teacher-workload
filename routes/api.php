<?php

use Illuminate\Support\Facades\Route;
use App\Models\Subject;
use App\Models\Workload;

// Auth middleware bilan (sanctum siz)
Route::middleware(['auth'])->group(function () {

    /**
     * Subject info - qolgan soatlar
     */
    Route::get('/workloads/subject-info/{subject}', function(Subject $subject, \Illuminate\Http\Request $request) {
        $academicYearId = $request->get('academic_year_id');

        if (!$academicYearId) {
            return response()->json([
                'success' => false,
                'error' => 'academic_year_id required'
            ], 400);
        }

        try {
            // Taqsimlangan soatlarni hisoblash
            $distributed = Workload::where('subject_id', $subject->id)
                ->where('academic_year_id', $academicYearId)
                ->selectRaw('
                    COALESCE(SUM(semester_1_lecture), 0) as lecture_1,
                    COALESCE(SUM(semester_1_practical), 0) as practical_1,
                    COALESCE(SUM(semester_1_laboratory), 0) as laboratory_1,
                    COALESCE(SUM(semester_1_seminar), 0) as seminar_1,
                    COALESCE(SUM(semester_2_lecture), 0) as lecture_2,
                    COALESCE(SUM(semester_2_practical), 0) as practical_2,
                    COALESCE(SUM(semester_2_laboratory), 0) as laboratory_2,
                    COALESCE(SUM(semester_2_seminar), 0) as seminar_2
                ')
                ->first();

            // Agar ma'lumot bo'lmasa
            if (!$distributed) {
                $distributed = (object)[
                    'lecture_1' => 0,
                    'practical_1' => 0,
                    'laboratory_1' => 0,
                    'seminar_1' => 0,
                    'lecture_2' => 0,
                    'practical_2' => 0,
                    'laboratory_2' => 0,
                    'seminar_2' => 0,
                ];
            }

            // Umumiy taqsimlangan soatlar
            $totalDistributedLecture = floatval($distributed->lecture_1 ?? 0) + floatval($distributed->lecture_2 ?? 0);
            $totalDistributedPractical = floatval($distributed->practical_1 ?? 0) + floatval($distributed->practical_2 ?? 0);
            $totalDistributedLaboratory = floatval($distributed->laboratory_1 ?? 0) + floatval($distributed->laboratory_2 ?? 0);
            $totalDistributedSeminar = floatval($distributed->seminar_1 ?? 0) + floatval($distributed->seminar_2 ?? 0);

            // Umumiy subject soatlari
            $totalSubjectLecture = floatval($subject->semester_1_lecture ?? 0) + floatval($subject->semester_2_lecture ?? 0);
            $totalSubjectPractical = floatval($subject->semester_1_practical ?? 0) + floatval($subject->semester_2_practical ?? 0);
            $totalSubjectLaboratory = floatval($subject->semester_1_laboratory ?? 0) + floatval($subject->semester_2_laboratory ?? 0);
            $totalSubjectSeminar = floatval($subject->semester_1_seminar ?? 0) + floatval($subject->semester_2_seminar ?? 0);

            // Qolgan soatlar
            $remaining = [
                'lecture' => max(0, $totalSubjectLecture - $totalDistributedLecture),
                'practical' => max(0, $totalSubjectPractical - $totalDistributedPractical),
                'laboratory' => max(0, $totalSubjectLaboratory - $totalDistributedLaboratory),
                'seminar' => max(0, $totalSubjectSeminar - $totalDistributedSeminar),
            ];

            // Jami soatlar
            $totalSubjectHours = $totalSubjectLecture + $totalSubjectPractical + $totalSubjectLaboratory + $totalSubjectSeminar;
            $totalDistributedHours = $totalDistributedLecture + $totalDistributedPractical + $totalDistributedLaboratory + $totalDistributedSeminar;

            // Taqsimlash foizi
            $distributionPercentage = $totalSubjectHours > 0
                ? round(($totalDistributedHours / $totalSubjectHours) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'subject' => [
                    'remaining' => $remaining,
                    'distributed' => [
                        'lecture' => $totalDistributedLecture,
                        'practical' => $totalDistributedPractical,
                        'laboratory' => $totalDistributedLaboratory,
                        'seminar' => $totalDistributedSeminar,
                    ],
                    'distribution_percentage' => $distributionPercentage,
                    'is_fully_distributed' => $distributionPercentage >= 100,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Subject info API error: ' . $e->getMessage(), [
                'subject_id' => $subject->id,
                'academic_year_id' => $academicYearId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    })->name('api.workloads.subject-info');
});
