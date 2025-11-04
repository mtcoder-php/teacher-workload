<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Workload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    /**
     * Fan statistikasini olish
     */
    public function getStatistics(Subject $subject, int $academicYearId): array
    {
        return [
            'semester_1' => $this->getSemesterStatistics($subject, $academicYearId, 1),
            'semester_2' => $this->getSemesterStatistics($subject, $academicYearId, 2),
            'general' => [
                'total_hours' => $subject->total_hours,
                'total_auditory_hours' => $subject->total_auditory_hours,
                'credit_hours' => $subject->credit_hours,
                'workloads_count' => $subject->workloads()
                    ->where('academic_year_id', $academicYearId)
                    ->count(),
            ],
        ];
    }

    /**
     * Semestr statistikasi
     */
    private function getSemesterStatistics(Subject $subject, int $academicYearId, int $semester): array
    {
        $semesterHours = $subject->getSemesterHours($semester);
        $distributed = $subject->getDistributedHours($academicYearId, $semester);
        $remaining = $subject->getRemainingHours($academicYearId, $semester);
        $percentage = $subject->getDistributionPercentage($academicYearId, $semester);

        return [
            'planned' => $semesterHours,
            'distributed' => $distributed,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'is_fully_distributed' => $subject->isFullyDistributed($academicYearId, $semester),
            'total_planned' => array_sum($semesterHours),
            'total_distributed' => array_sum($distributed),
            'total_remaining' => array_sum($remaining),
        ];
    }

    /**
     * Fanni taqsimlash uchun validatsiya
     */
    public function validateDistribution(
        Subject $subject,
        int $academicYearId,
        int $semester,
        array $hours
    ): array {
        $errors = [];
        $remaining = $subject->getRemainingHours($academicYearId, $semester);

        foreach (['lecture', 'practical', 'laboratory', 'seminar', 'practice', 'exam', 'test'] as $type) {
            $requested = $hours[$type] ?? 0;
            $available = $remaining[$type] ?? 0;

            if ($requested > $available) {
                $errors[$type] = "Qolgan soat ({$available}) yetarli emas. Siz {$requested} soat talab qildingiz.";
            }
        }

        return $errors;
    }

    /**
     * Fan soatlarini hisoblash
     */
    public function calculateHours(Subject $subject): array
    {
        return [
            'semester_1' => [
                'total' => $subject->semester_1_total_hours,
                'auditory' => $subject->semester_1_auditory_hours,
                'breakdown' => [
                    'lecture' => $subject->semester_1_lecture,
                    'practical' => $subject->semester_1_practical,
                    'laboratory' => $subject->semester_1_laboratory,
                    'seminar' => $subject->semester_1_seminar,
                    'practice' => $subject->semester_1_practice,
                    'exam' => $subject->semester_1_exam,
                    'test' => $subject->semester_1_test,
                ],
            ],
            'semester_2' => [
                'total' => $subject->semester_2_total_hours,
                'auditory' => $subject->semester_2_auditory_hours,
                'breakdown' => [
                    'lecture' => $subject->semester_2_lecture,
                    'practical' => $subject->semester_2_practical,
                    'laboratory' => $subject->semester_2_laboratory,
                    'seminar' => $subject->semester_2_seminar,
                    'practice' => $subject->semester_2_practice,
                    'exam' => $subject->semester_2_exam,
                    'test' => $subject->semester_2_test,
                ],
            ],
            'additional' => [
                'coursework' => $subject->coursework_hours,
                'diploma' => $subject->diploma_hours,
                'consultation' => $subject->consultation_hours,
            ],
            'grand_total' => $subject->total_hours,
        ];
    }

    /**
     * Fanlarni guruhlar uchun patok qilish imkoniyatini tekshirish
     */
    public function checkPotokPossibility(Subject $subject, array $groupIds): array
    {
        $groupsCount = count($groupIds);

        return [
            'can_be_potok' => $subject->can_be_potok,
            'min_groups_required' => $subject->min_groups_for_potok,
            'current_groups' => $groupsCount,
            'is_possible' => $subject->canCreatePotok($groupsCount),
            'message' => $this->getPotokMessage($subject, $groupsCount),
        ];
    }

    /**
     * Patok xabari
     */
    private function getPotokMessage(Subject $subject, int $groupsCount): string
    {
        if (!$subject->can_be_potok) {
            return "Bu fan patok qilinmaydi.";
        }

        if ($groupsCount < $subject->min_groups_for_potok) {
            $needed = $subject->min_groups_for_potok - $groupsCount;
            return "Patok qilish uchun yana {$needed} ta guruh kerak.";
        }

        return "Patok qilish mumkin!";
    }

    /**
     * Kafedra fanlarini tahlil qilish
     */
    public function analyzeDepartmentSubjects(int $departmentId, int $academicYearId): array
    {
        $subjects = Subject::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();

        $analysis = [
            'total_subjects' => $subjects->count(),
            'by_course_level' => [],
            'by_type' => [],
            'distribution_status' => [
                'fully_distributed' => 0,
                'partially_distributed' => 0,
                'not_distributed' => 0,
            ],
            'total_hours' => [
                'planned' => 0,
                'distributed' => 0,
                'remaining' => 0,
            ],
        ];

        foreach ($subjects as $subject) {
            // Kurs darajasi bo'yicha
            $level = $subject->course_level;
            if (!isset($analysis['by_course_level'][$level])) {
                $analysis['by_course_level'][$level] = 0;
            }
            $analysis['by_course_level'][$level]++;

            // Tur bo'yicha
            $type = $subject->subject_type;
            if (!isset($analysis['by_type'][$type])) {
                $analysis['by_type'][$type] = 0;
            }
            $analysis['by_type'][$type]++;

            // Taqsimlash holati
            $semester1Distributed = $subject->isFullyDistributed($academicYearId, 1);
            $semester2Distributed = $subject->isFullyDistributed($academicYearId, 2);

            if ($semester1Distributed && $semester2Distributed) {
                $analysis['distribution_status']['fully_distributed']++;
            } elseif (!$semester1Distributed && !$semester2Distributed) {
                $analysis['distribution_status']['not_distributed']++;
            } else {
                $analysis['distribution_status']['partially_distributed']++;
            }

            // Jami soatlar
            $analysis['total_hours']['planned'] += $subject->total_hours;
            
            $distributed1 = array_sum($subject->getDistributedHours($academicYearId, 1));
            $distributed2 = array_sum($subject->getDistributedHours($academicYearId, 2));
            $analysis['total_hours']['distributed'] += ($distributed1 + $distributed2);
        }

        $analysis['total_hours']['remaining'] = 
            $analysis['total_hours']['planned'] - $analysis['total_hours']['distributed'];

        $analysis['distribution_percentage'] = 
            $analysis['total_hours']['planned'] > 0
                ? round(($analysis['total_hours']['distributed'] / $analysis['total_hours']['planned']) * 100, 2)
                : 0;

        return $analysis;
    }

    /**
     * Fan yuklamasini o'chirish
     */
    public function deleteSubjectWorkloads(Subject $subject, int $academicYearId): int
    {
        return $subject->workloads()
            ->where('academic_year_id', $academicYearId)
            ->delete();
    }

    /**
     * Fanlarni import qilish
     */
    public function importSubjects(array $subjectsData): array
    {
        $created = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($subjectsData as $index => $data) {
                try {
                    $subject = Subject::where('code', $data['code'])->first();

                    if ($subject) {
                        $subject->update($data);
                        $updated++;
                    } else {
                        Subject::create($data);
                        $created++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $index + 1,
                        'code' => $data['code'] ?? 'N/A',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
            'total' => count($subjectsData),
        ];
    }

    /**
     * Fanlarni export qilish
     */
    public function exportSubjects(array $filters = []): Collection
    {
        $query = Subject::with(['department', 'direction']);

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['direction_id'])) {
            $query->where('direction_id', $filters['direction_id']);
        }

        if (!empty($filters['course_level'])) {
            $query->where('course_level', $filters['course_level']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->get();
    }

    /**
     * Fan nusxasini yaratish
     */
    public function duplicateSubject(Subject $subject, array $newData = []): Subject
    {
        $data = $subject->toArray();
        
        // O'chirish kerak bo'lgan maydonlar
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);

        // Yangi kod yaratish
        $data['code'] = $newData['code'] ?? $subject->code . '-COPY';
        $data['name'] = $newData['name'] ?? $subject->name . ' (Nusxa)';

        // Qo'shimcha yangi ma'lumotlar
        $data = array_merge($data, $newData);

        return Subject::create($data);
    }

    /**
     * Fanlarni massaviy yangilash
     */
    public function bulkUpdate(array $subjectIds, array $updates): int
    {
        return Subject::whereIn('id', $subjectIds)->update($updates);
    }

    /**
     * Fanlarni massaviy o'chirish
     */
    public function bulkDelete(array $subjectIds): int
    {
        // Faqat yuklamasi bo'lmagan fanlarni o'chirish
        $subjects = Subject::whereIn('id', $subjectIds)
            ->whereDoesntHave('workloads')
            ->get();

        $deleted = 0;
        foreach ($subjects as $subject) {
            if ($subject->delete()) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Fanning to'liq ma'lumotlarini olish
     */
    public function getFullSubjectData(Subject $subject, int $academicYearId): array
    {
        return [
            'subject' => $subject->load(['department.faculty', 'direction']),
            'hours' => $this->calculateHours($subject),
            'statistics' => $this->getStatistics($subject, $academicYearId),
            'workloads' => $subject->workloads()
                ->where('academic_year_id', $academicYearId)
                ->with(['teacher.user', 'group'])
                ->get(),
        ];
    }
}