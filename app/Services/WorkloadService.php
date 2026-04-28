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
    protected RatingService $ratingService; // ← qo'shing

    public function __construct(
        WorkloadValidationService $validationService,
        RatingService $ratingService              // ← qo'shing
    ) {
        $this->validationService = $validationService;
        $this->ratingService     = $ratingService; // ← qo'shing
    }

    /**
     * Yangi yuklama yaratish
     */
    public function createWorkload(array $data): Workload
    {
        DB::beginTransaction();

        try {
            // ─── 1. Academic year ────────────────────────────────────────────
            if (empty($data['academic_year_id'])) {
                $activeYear = AcademicYear::where('is_active', true)->first();
                if (!$activeYear) {
                    throw new \Exception('Faol o\'quv yili topilmadi');
                }
                $data['academic_year_id'] = $activeYear->id;
            }

            // ─── 2. direction_id ni aniqlash ─────────────────────────────────
            if (empty($data['direction_id'])) {
                if (!empty($data['direction_ids']) && is_array($data['direction_ids'])) {
                    $data['direction_id'] = $data['direction_ids'][0];
                } elseif (!empty($data['group_ids'])) {
                    $firstGroup = Group::find($data['group_ids'][0]);
                    if ($firstGroup) {
                        $data['direction_id'] = $firstGroup->direction_id;
                    }
                }
            }

            if (empty($data['direction_id'])) {
                throw new \Exception('Yo\'nalish aniqlanmadi. Iltimos, yo\'nalish tanlang.');
            }

            $isPotok   = (bool)($data['is_potok'] ?? false);
            $groupIds  = $data['group_ids'] ?? [];

            // ─── 3. Potoksiz + ko'p guruh → har biri uchun alohida yuklama ──
            if (!$isPotok && count($groupIds) > 1) {
                $firstWorkload = null;
                foreach ($groupIds as $groupId) {
                    $singleData              = $data;
                    $singleData['group_ids'] = [$groupId];
                    // Har guruh uchun direction_id ni shu guruhdan olish
                    $group = Group::find($groupId);
                    if ($group) {
                        $singleData['direction_id'] = $group->direction_id;
                    }
                    $workload = $this->createSingleWorkload($singleData, false);
                    if (!$firstWorkload) {
                        $firstWorkload = $workload;
                    }
                }
                DB::commit();
                return $firstWorkload;
            }

            // ─── 4. Oddiy yaratish (1 guruh yoki potok) ──────────────────────
            $workload = $this->createSingleWorkload($data, $isPotok);

            DB::commit();
            return $workload;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Workload creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Bitta yuklama yaratish (ichki metod)
     */
    private function createSingleWorkload(array $data, bool $isPotok): Workload
    {
        // Validatsiya
        $validationErrors = $this->validationService->validateStoreRequest($data);
        if (!empty($validationErrors)) {
            throw new \Exception(implode('; ', $validationErrors));
        }

        // Guruhlar
        $groups = Group::whereIn('id', $data['group_ids'])->get();
        if ($groups->isEmpty()) {
            throw new \Exception('Guruhlar topilmadi');
        }
        $totalStudents = $groups->sum('student_count');

        // Reyting
        $rating = $data['rating'] ?? ($totalStudents > 0 ? round($totalStudents / 2, 2) : 0);

        // Potok kodi
        if ($isPotok && empty($data['potok_code'])) {
            $data['potok_code'] = 'POTOK-' . $data['subject_id'] . '-' . strtoupper(uniqid());
        }

        // Workload turi
        $workloadType = $this->determineWorkloadType($data, $isPotok);

        // Workload yaratish
        $workload = Workload::create([
            'department_id' => $data['department_id'],
            'direction_id' => $data['direction_id'],
            'teacher_id' => $data['teacher_id'],
            'subject_id' => $data['subject_id'],
            'academic_year_id' => $data['academic_year_id'],

            'is_potok' => $isPotok,
            'potok_code' => $data['potok_code'] ?? null,
            'workload_type' => $workloadType,
            'is_potok_remainder' => false,
            'parent_potok_id' => $data['parent_potok_id'] ?? null,

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

            'total_students' => $totalStudents,
            'rating' => $rating,
            'has_rating' => $data['has_rating'] ?? false,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null,
        ]);

        // Guruhni bog'lash
        $workload->groups()->attach($data['group_ids']);

        Log::info('Workload created', [
            'id' => $workload->id,
            'type' => $workloadType,
            'is_potok' => $isPotok,
            'groups' => count($data['group_ids']),
        ]);

        return $workload;
    }
        /**
         * Workload turini aniqlash
         */
        private function determineWorkloadType(array $data, bool $isPotok): string
    {
        if ($isPotok) {
            return 'lecture_only';
        }

        $hasLecture   = ($data['semester_1_lecture']  ?? 0) > 0 || ($data['semester_2_lecture']  ?? 0) > 0;
        $hasPractice  = ($data['semester_1_practical'] ?? 0) > 0 || ($data['semester_2_practical'] ?? 0) > 0;
        $hasLab       = ($data['semester_1_laboratory']?? 0) > 0 || ($data['semester_2_laboratory']?? 0) > 0;
        $hasSeminar   = ($data['semester_1_seminar']   ?? 0) > 0 || ($data['semester_2_seminar']   ?? 0) > 0;

        if ($hasLecture && ($hasPractice || $hasLab || $hasSeminar)) return 'full';
        if ($hasLecture)                                              return 'lecture_only';
        if ($hasPractice || $hasLab || $hasSeminar)                  return 'practice_only';

        return 'combined';
    }


        /**
         * Yuklamani yangilash
         *
         * Har doim:      teacher_id, notes
         * Faqat draft:   barcha soat maydonlari
         */
        public function updateWorkload(Workload $workload, array $data): Workload
    {
        DB::beginTransaction();

        try {
            $updateData = [];

            // O'qituvchi va izoh — har doim yangilanadi
            if (isset($data['teacher_id'])) {
                $updateData['teacher_id'] = $data['teacher_id'];
            }
            if (array_key_exists('notes', $data)) {
                $updateData['notes'] = $data['notes'];
            }

            // Soatlar — faqat draft statusda
            if ($workload->status === 'draft') {
                $hourFields = [
                    'semester_1_lecture','semester_1_practical','semester_1_laboratory',
                    'semester_1_seminar','semester_1_practice','semester_1_exam','semester_1_test',
                    'semester_2_lecture','semester_2_practical','semester_2_laboratory',
                    'semester_2_seminar','semester_2_practice','semester_2_exam','semester_2_test',
                    'coursework_hours','diploma_hours','consultation_hours',
                ];

                foreach ($hourFields as $field) {
                    if (!isset($data[$field])) continue;

                    $newValue = (float)$data[$field];

                    // Potok: faqat ma'ruza soatlari o'zgartirilsin
                    if ($workload->is_potok && !str_contains($field, 'lecture')) {
                        continue;
                    }

                    // Potok: ma'ruza soatlarini kamaytirish taqiqlangan
                    if ($workload->is_potok && str_contains($field, 'lecture')) {
                        $original = (float)($workload->{$field} ?? 0);
                        if ($newValue < $original) {
                            throw new \Exception(
                                "Potok yuklamada {$field} soatlarini kamaytirish mumkin emas. " .
                                "Joriy: {$original}, Yangi: {$newValue}"
                            );
                        }
                    }

                    $updateData[$field] = $newValue;
                }

                // Jami soatlarni qayta hisoblash
                if (!empty($updateData)) {
                    $total = 0;
                    foreach ($hourFields as $field) {
                        $total += (float)($updateData[$field] ?? $workload->{$field} ?? 0);
                    }
                    $updateData['total_hours'] = $total;
                }
            }

            if (!empty($updateData)) {
                $workload->update($updateData);
            }

            DB::commit();
            return $workload->fresh();

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
        $workload->delete();
        return true;
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

            $groupIds = $data['group_ids'];
            foreach ($groupIds as $groupId) {
                $check = $this->validationService->canCreatePotokRemainder(
                    $groupId, $potok->subject_id, $potok->academic_year_id
                );
                if (!$check['can_create']) {
                    throw new \Exception($check['message']);
                }
            }

            $remainders = [];
            foreach ($groupIds as $groupId) {
                $group = Group::findOrFail($groupId);

                $remainder = Workload::create([
                    'department_id'       => $potok->department_id,
                    'direction_id'        => $group->direction_id ?? $potok->direction_id,
                    'teacher_id'          => $data['teacher_id'] ?? $potok->teacher_id,
                    'subject_id'          => $potok->subject_id,
                    'academic_year_id'    => $potok->academic_year_id,
                    'is_potok'            => false,
                    'potok_code'          => $potok->potok_code . '-REM',
                    'workload_type'       => 'practice_only',
                    'is_potok_remainder'  => true,
                    'parent_potok_id'     => $potok->id,
                    'semester_1_practical'  => $data['semester_1_practical']  ?? 0,
                    'semester_1_laboratory' => $data['semester_1_laboratory'] ?? 0,
                    'semester_2_practical'  => $data['semester_2_practical']  ?? 0,
                    'semester_2_laboratory' => $data['semester_2_laboratory'] ?? 0,
                    'total_students'        => $group->student_count ?? 0,
                    'status'                => 'draft',
                ]);

                $remainder->groups()->attach([$groupId]);
                $remainders[] = $remainder;
            }

            DB::commit();
            return $remainders[0];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    }
