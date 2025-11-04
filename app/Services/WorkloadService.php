<?php

namespace App\Services;

use App\Models\Workload;
use App\Models\Group;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkloadService
{
    protected WorkloadValidationService $validationService;

    public function __construct(WorkloadValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Yangi yuklama yaratish
     */
    public function createWorkload(array $data): Workload
    {
        DB::beginTransaction();

        try {
            // Academic year
            if (empty($data['academic_year_id'])) {
                $activeYear = AcademicYear::where('is_active', true)->first();
                if (!$activeYear) {
                    throw new \Exception('Faol o\'quv yili topilmadi');
                }
                $data['academic_year_id'] = $activeYear->id;
            }

            // Validatsiya
            $validationErrors = $this->validationService->validateStoreRequest($data);
            if (!empty($validationErrors)) {
                throw new \Exception(implode('; ', $validationErrors));
            }

            // Guruhlar va talabalar soni
            $groups = Group::whereIn('id', $data['group_ids'])->get();
            $totalStudents = $groups->sum('student_count');

            // Reyting avtomatik
            $rating = $data['rating'] ?? ($totalStudents / 2);

            // Potok kodi
            $isPotok = $data['is_potok'] ?? false;
            if ($isPotok && empty($data['potok_code'])) {
                $data['potok_code'] = 'POTOK-' . $data['subject_id'] . '-' . strtoupper(uniqid());
            }

            // Workload type aniqlash
            $workloadType = $this->determineWorkloadType($data, $isPotok);

            // Yuklama yaratish
            $workload = Workload::create([
                'department_id' => $data['department_id'],
                'direction_id' => $data['direction_id'],
                'teacher_id' => $data['teacher_id'],
                'subject_id' => $data['subject_id'],
                'academic_year_id' => $data['academic_year_id'],

                // Potok
                'is_potok' => $isPotok,
                'potok_code' => $data['potok_code'] ?? null,
                'workload_type' => $workloadType,
                'is_potok_remainder' => false,
                'parent_potok_id' => $data['parent_potok_id'] ?? null,

                // Soatlar
                'semester_1_lecture' => $data['semester_1_lecture'] ?? 0,
                'semester_1_practical' => $data['semester_1_practical'] ?? 0,
                'semester_1_laboratory' => $data['semester_1_laboratory'] ?? 0,
                'semester_1_seminar' => $data['semester_1_seminar'] ?? 0,
                'semester_1_practice' => $data['semester_1_practice'] ?? 0,
                'semester_1_exam' => $data['semester_1_exam'] ?? 0,
                'semester_1_test' => $data['semester_1_test'] ?? 0,

                'semester_2_lecture' => $data['semester_2_lecture'] ?? 0,
                'semester_2_practical' => $data['semester_2_practical'] ?? 0,
                'semester_2_laboratory' => $data['semester_2_laboratory'] ?? 0,
                'semester_2_seminar' => $data['semester_2_seminar'] ?? 0,
                'semester_2_practice' => $data['semester_2_practice'] ?? 0,
                'semester_2_exam' => $data['semester_2_exam'] ?? 0,
                'semester_2_test' => $data['semester_2_test'] ?? 0,

                'coursework_hours' => $data['coursework_hours'] ?? 0,
                'diploma_hours' => $data['diploma_hours'] ?? 0,
                'consultation_hours' => $data['consultation_hours'] ?? 0,

                // Statistika
                'total_students' => $totalStudents,
                'rating' => $rating,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            // Guruhlarni bog'lash
            $workload->groups()->attach($data['group_ids']);

            DB::commit();

            Log::info('Workload created', [
                'id' => $workload->id,
                'type' => $workloadType,
                'is_potok' => $isPotok,
                'groups' => count($data['group_ids']),
            ]);

            return $workload;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Workload creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Potok qoldig'i (amaliy soatlar) yaratish
     */
    public function createPotokRemainder(array $data, int $potokId): Workload
    {
        DB::beginTransaction();

        try {
            $potok = Workload::findOrFail($potokId);

            if (!$potok->is_potok || $potok->workload_type !== 'lecture_only') {
                throw new \Exception('Noto\'g\'ri potok yuklama');
            }

            // Guruhlarni tekshirish
            $groupIds = $data['group_ids'];
            foreach ($groupIds as $groupId) {
                $check = $this->validationService->canCreatePotokRemainder(
                    $groupId,
                    $potok->subject_id,
                    $potok->academic_year_id
                );

                if (!$check['can_create']) {
                    throw new \Exception($check['message']);
                }
            }

            // Qoldig'i yaratish (har guruh uchun alohida)
            $remainders = [];
            foreach ($groupIds as $groupId) {
                $group = Group::findOrFail($groupId);

                $remainder = Workload::create([
                    'department_id' => $potok->department_id,
                    'direction_id' => $potok->direction_id,
                    'teacher_id' => $data['teacher_id'] ?? $potok->teacher_id,
                    'subject_id' => $potok->subject_id,
                    'academic_year_id' => $potok->academic_year_id,

                    // Potok bog'liqlik
                    'is_potok' => false,
                    'potok_code' => $potok->potok_code . '-R',
                    'workload_type' => 'practice_only',
                    'is_potok_remainder' => true,
                    'parent_potok_id' => $potok->id,

                    // Faqat amaliy soatlar
                    'semester_1_lecture' => 0,
                    'semester_1_practical' => $data['semester_1_practical'] ?? 0,
                    'semester_1_laboratory' => $data['semester_1_laboratory'] ?? 0,
                    'semester_1_seminar' => $data['semester_1_seminar'] ?? 0,
                    'semester_1_practice' => $data['semester_1_practice'] ?? 0,
                    'semester_1_exam' => $data['semester_1_exam'] ?? 0,
                    'semester_1_test' => $data['semester_1_test'] ?? 0,

                    'semester_2_lecture' => 0,
                    'semester_2_practical' => $data['semester_2_practical'] ?? 0,
                    'semester_2_laboratory' => $data['semester_2_laboratory'] ?? 0,
                    'semester_2_seminar' => $data['semester_2_seminar'] ?? 0,
                    'semester_2_practice' => $data['semester_2_practice'] ?? 0,
                    'semester_2_exam' => $data['semester_2_exam'] ?? 0,
                    'semester_2_test' => $data['semester_2_test'] ?? 0,

                    'total_students' => $group->student_count,
                    'rating' => $group->student_count / 2,
                    'status' => 'draft',
                ]);

                $remainder->groups()->attach([$groupId]);
                $remainders[] = $remainder;
            }

            DB::commit();

            Log::info('Potok remainder created', [
                'potok_id' => $potok->id,
                'remainders' => count($remainders),
            ]);

            return $remainders[0]; // Birinchisini qaytaramiz

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Potok remainder creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Yuklama turini aniqlash
     */
    protected function determineWorkloadType(array $data, bool $isPotok): string
    {
        $hasLecture = ($data['semester_1_lecture'] ?? 0) > 0 || ($data['semester_2_lecture'] ?? 0) > 0;
        $hasPractice = ($data['semester_1_practical'] ?? 0) > 0 || ($data['semester_2_practical'] ?? 0) > 0;
        $hasLaboratory = ($data['semester_1_laboratory'] ?? 0) > 0 || ($data['semester_2_laboratory'] ?? 0) > 0;
        $hasSeminar = ($data['semester_1_seminar'] ?? 0) > 0 || ($data['semester_2_seminar'] ?? 0) > 0;

        if ($isPotok) {
            // Potok faqat ma'ruza bo'lishi kerak
            return 'lecture_only';
        }

        if ($hasLecture && ($hasPractice || $hasLaboratory || $hasSeminar)) {
            return 'full';
        } elseif ($hasLecture) {
            return 'lecture_only';
        } elseif ($hasPractice || $hasLaboratory || $hasSeminar) {
            return 'practice_only';
        }

        return 'combined';
    }

    /**
     * Yuklamani yangilash
     */
    public function updateWorkload(Workload $workload, array $data): Workload
    {
        DB::beginTransaction();

        try {
            // Faqat ma'lum maydonlarni yangilash mumkin
            $updateData = [];

            if (isset($data['teacher_id'])) {
                $updateData['teacher_id'] = $data['teacher_id'];
            }

            if (isset($data['notes'])) {
                $updateData['notes'] = $data['notes'];
            }

            // Potok bo'lmasa, soatlarni yangilash mumkin
            if (!$workload->is_potok && $workload->status === 'draft') {
                $hourFields = [
                    'semester_1_lecture', 'semester_1_practical',
                    'semester_1_laboratory', 'semester_1_seminar',
                    'semester_1_practice', 'semester_1_exam', 'semester_1_test',
                    'semester_2_lecture', 'semester_2_practical',
                    'semester_2_laboratory', 'semester_2_seminar',
                    'semester_2_practice', 'semester_2_exam', 'semester_2_test',
                    'coursework_hours', 'diploma_hours', 'consultation_hours',
                ];

                foreach ($hourFields as $field) {
                    if (isset($data[$field])) {
                        $updateData[$field] = $data[$field];
                    }
                }
            }

            if (!empty($updateData)) {
                $workload->update($updateData);
            }

            DB::commit();

            return $workload;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Yuklamani o'chirish
     */
    public function deleteWorkload(Workload $workload): bool
    {
        if ($workload->status !== 'draft') {
            throw new \Exception('Faqat qoralama yuklamalarni o\'chirish mumkin!');
        }

        DB::beginTransaction();

        try {
            // Agar bu potok bo'lsa, qoldig'larini ham o'chirish
            if ($workload->is_potok) {
                Workload::where('parent_potok_id', $workload->id)->delete();
            }

            $workload->groups()->detach();
            $workload->delete();

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
