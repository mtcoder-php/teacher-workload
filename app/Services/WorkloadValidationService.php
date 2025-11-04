<?php

namespace App\Services;

use App\Models\Workload;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkloadValidationService
{
    /**
     * Guruhning fan bo'yicha holatini tekshirish
     */
    public function checkGroupStatus(int $groupId, int $subjectId, int $academicYearId): array
    {
        $group = Group::findOrFail($groupId);
        $subject = Subject::findOrFail($subjectId);

        // Shu guruhning shu fandan barcha yuklamalari
        $existingWorkloads = Workload::where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->whereHas('groups', function ($query) use ($groupId) {
                $query->where('groups.id', $groupId);
            })
            ->with('groups')
            ->get();

        $status = [
            'group_id' => $groupId,
            'group_name' => $group->name,
            'group_code' => $group->code,

            // Holatlar
            'has_workload' => false,
            'is_in_potok' => false,
            'potok_code' => null,
            'potok_id' => null,

            // Mavjud soatlar
            'has_lecture' => false,
            'has_practice' => false,
            'has_laboratory' => false,
            'has_seminar' => false,

            // Qolgan soatlar (subjectdan)
            'remaining_lecture' => $subject->semester_1_lecture + $subject->semester_2_lecture,
            'remaining_practice' => $subject->semester_1_practical + $subject->semester_2_practical,
            'remaining_laboratory' => $subject->semester_1_laboratory + $subject->semester_2_laboratory,
            'remaining_seminar' => $subject->semester_1_seminar + $subject->semester_2_seminar,

            // Ruxsatlar
            'can_create_potok' => true,
            'can_create_regular' => true,
            'can_create_remainder' => false,
            'can_join_existing_potok' => false,

            'warnings' => [],
            'errors' => [],
        ];

        if ($existingWorkloads->isEmpty()) {
            // Hech qanday yuklama yo'q - hammasi mumkin
            return $status;
        }

        $status['has_workload'] = true;

        foreach ($existingWorkloads as $workload) {
            // Ma'ruza soatlari borligini tekshirish
            $lectureHours = $workload->semester_1_lecture + $workload->semester_2_lecture;
            $practiceHours = $workload->semester_1_practical + $workload->semester_2_practical;
            $labHours = $workload->semester_1_laboratory + $workload->semester_2_laboratory;
            $seminarHours = $workload->semester_1_seminar + $workload->semester_2_seminar;

            if ($lectureHours > 0) {
                $status['has_lecture'] = true;
                $status['remaining_lecture'] = max(0, $status['remaining_lecture'] - $lectureHours);
            }
            if ($practiceHours > 0) {
                $status['has_practice'] = true;
                $status['remaining_practice'] = max(0, $status['remaining_practice'] - $practiceHours);
            }
            if ($labHours > 0) {
                $status['has_laboratory'] = true;
                $status['remaining_laboratory'] = max(0, $status['remaining_laboratory'] - $labHours);
            }
            if ($seminarHours > 0) {
                $status['has_seminar'] = true;
                $status['remaining_seminar'] = max(0, $status['remaining_seminar'] - $seminarHours);
            }

            // Potok tekshiruvi
            if ($workload->is_potok) {
                $status['is_in_potok'] = true;
                $status['potok_code'] = $workload->potok_code;
                $status['potok_id'] = $workload->id;

                if ($workload->workload_type === 'lecture_only') {
                    // Bu potok faqat ma'ruza uchun
                    $status['can_create_potok'] = false;
                    $status['can_create_regular'] = true; // amaliy uchun mumkin
                    $status['can_create_remainder'] = true;
                    $status['warnings'][] = "Bu guruh '{$workload->potok_code}' potokida (ma'ruza). Amaliy soatlar uchun alohida yuklama yaratishingiz mumkin.";
                } else {
                    // To'liq potok
                    $status['can_create_potok'] = false;
                    $status['can_create_regular'] = false;
                    $status['errors'][] = "Bu guruh '{$workload->potok_code}' potokida va to'liq yuklama berilgan.";
                }
            } else {
                // Oddiy yuklama
                if ($status['has_lecture'] && $status['has_practice'] && $status['has_laboratory'] && $status['has_seminar']) {
                    $status['can_create_potok'] = false;
                    $status['can_create_regular'] = false;
                    $status['errors'][] = "Bu guruhga bu fandan to'liq yuklama allaqachon berilgan.";
                } else if ($status['has_lecture']) {
                    $status['can_create_potok'] = false;
                    $status['warnings'][] = "Ma'ruza allaqachon berilgan. Faqat qolgan soatlar uchun yuklama yaratishingiz mumkin.";
                }
            }
        }

        // Qolgan soatlar bo'lsa, remainder yaratish mumkin
        if ($status['remaining_practice'] > 0 || $status['remaining_laboratory'] > 0 || $status['remaining_seminar'] > 0) {
            $status['can_create_remainder'] = true;
        }

        return $status;
    }

    /**
     * Ko'p guruhlarni tekshirish
     */
    public function checkMultipleGroupsStatus(array $groupIds, int $subjectId, int $academicYearId): array
    {
        $results = [];
        foreach ($groupIds as $groupId) {
            $results[$groupId] = $this->checkGroupStatus($groupId, $subjectId, $academicYearId);
        }
        return $results;
    }

    /**
     * Potok yaratish mumkinligini tekshirish
     */
    public function canCreatePotok(array $groupIds, int $subjectId, int $academicYearId): array
    {
        if (count($groupIds) < 2) {
            return [
                'can_create' => false,
                'message' => 'Potok uchun kamida 2 ta guruh kerak',
            ];
        }

        if (count($groupIds) > 10) {
            return [
                'can_create' => false,
                'message' => 'Potok uchun maksimal 10 ta guruh mumkin',
            ];
        }

        // Barcha guruhlarni tekshirish
        $statuses = $this->checkMultipleGroupsStatus($groupIds, $subjectId, $academicYearId);

        foreach ($statuses as $status) {
            // Agar guruhda allaqachon ma'ruza bo'lsa
            if ($status['has_lecture']) {
                return [
                    'can_create' => false,
                    'message' => "Guruh {$status['group_name']}: Ma'ruza allaqachon berilgan. Potokka qo'shib bo'lmaydi.",
                    'problem_group' => $status['group_name'],
                ];
            }

            // Agar guruh boshqa potokda bo'lsa
            if ($status['is_in_potok']) {
                return [
                    'can_create' => false,
                    'message' => "Guruh {$status['group_name']}: Allaqachon '{$status['potok_code']}' potokida.",
                    'problem_group' => $status['group_name'],
                ];
            }

            if (!$status['can_create_potok']) {
                return [
                    'can_create' => false,
                    'message' => "Guruh {$status['group_name']}: " . implode(', ', $status['errors']),
                    'problem_group' => $status['group_name'],
                ];
            }
        }

        return [
            'can_create' => true,
            'message' => 'Potok yaratish mumkin',
        ];
    }

    /**
     * Oddiy yuklama yaratish mumkinligini tekshirish
     */
    public function canCreateRegular(
        int $groupId,
        int $subjectId,
        int $academicYearId,
        array $hours
    ): array {
        $status = $this->checkGroupStatus($groupId, $subjectId, $academicYearId);

        // Agar ma'ruza kiritilmoqchi bo'lsa va guruh potokda bo'lsa
        if (($hours['semester_1_lecture'] > 0 || $hours['semester_2_lecture'] > 0) && $status['is_in_potok']) {
            return [
                'can_create' => false,
                'message' => "Bu guruh '{$status['potok_code']}' potokida. Ma'ruza uchun potok mavjud.",
            ];
        }

        // Agar ma'ruza kiritilmoqchi bo'lsa va allaqachon ma'ruza berilgan bo'lsa
        if (($hours['semester_1_lecture'] > 0 || $hours['semester_2_lecture'] > 0) && $status['has_lecture']) {
            return [
                'can_create' => false,
                'message' => "Bu guruhga allaqachon ma'ruza berilgan. Takror yuklama yaratib bo'lmaydi.",
            ];
        }

        // Agar to'liq yuklama berilgan bo'lsa
        if (!$status['can_create_regular'] && count($status['errors']) > 0) {
            return [
                'can_create' => false,
                'message' => implode(', ', $status['errors']),
            ];
        }

        return [
            'can_create' => true,
            'message' => 'Yuklama yaratish mumkin',
        ];
    }

    /**
     * Potok qoldig'i (amaliy) yaratish mumkinligini tekshirish
     */
    public function canCreatePotokRemainder(int $groupId, int $subjectId, int $academicYearId): array
    {
        $status = $this->checkGroupStatus($groupId, $subjectId, $academicYearId);

        if (!$status['is_in_potok']) {
            return [
                'can_create' => false,
                'message' => "Bu guruh potokda emas.",
            ];
        }

        if (!$status['can_create_remainder']) {
            return [
                'can_create' => false,
                'message' => "Qolgan soatlar yo'q yoki allaqachon berilgan.",
            ];
        }

        return [
            'can_create' => true,
            'message' => 'Potok qoldig\'i (amaliy soatlar) uchun yuklama yaratish mumkin',
            'potok_id' => $status['potok_id'],
            'remaining_hours' => [
                'practice' => $status['remaining_practice'],
                'laboratory' => $status['remaining_laboratory'],
                'seminar' => $status['remaining_seminar'],
            ],
        ];
    }

    /**
     * Form submit validatsiyasi
     */
    public function validateStoreRequest(array $data): array
    {
        $errors = [];

        // Academic year tekshirish
        if (empty($data['academic_year_id'])) {
            $errors['academic_year_id'] = 'O\'quv yili topilmadi';
        }

    // Potok validatsiyasi
if ($data['is_potok'] ?? false) {
$potokCheck = $this->canCreatePotok(
$data['group_ids'],
$data['subject_id'],
$data['academic_year_id']
);

if (!$potokCheck['can_create']) {
$errors['is_potok'] = $potokCheck['message'];
}

// Potok uchun FAQAT ma'ruza soatlari bo'lishi kerak
if (($data['semester_1_practical'] ?? 0) > 0 ||
    ($data['semester_1_laboratory'] ?? 0) > 0 ||
    ($data['semester_1_seminar'] ?? 0) > 0 ||
    ($data['semester_2_practical'] ?? 0) > 0 ||
    ($data['semester_2_laboratory'] ?? 0) > 0 ||
    ($data['semester_2_seminar'] ?? 0) > 0) {
    $errors['is_potok'] = 'Potok uchun faqat ma\'ruza soatlari kiritilishi kerak. Amaliy, laboratoriya va seminar soatlari guruhlar uchun alohida beriladi.';
}

// Potokda ma'ruza soatlari bo'lishi shart
if (($data['semester_1_lecture'] ?? 0) <= 0 && ($data['semester_2_lecture'] ?? 0) <= 0) {
    $errors['is_potok'] = 'Potok uchun ma\'ruza soatlari kiritilishi shart.';
}
} else {
    // Oddiy yuklama uchun har bir guruhni tekshirish
    foreach ($data['group_ids'] as $groupId) {
        $hours = [
            'semester_1_lecture' => $data['semester_1_lecture'] ?? 0,
            'semester_2_lecture' => $data['semester_2_lecture'] ?? 0,
        ];

        $check = $this->canCreateRegular(
            $groupId,
            $data['subject_id'],
            $data['academic_year_id'],
            $hours
        );

        if (!$check['can_create']) {
            $errors['group_ids'] = $check['message'];
            break;
        }
    }
}

return $errors;
}

/**
 * Mavjud potokni topish (guruhlar uchun)
 */
public function findExistingPotok(array $groupIds, int $subjectId, int $academicYearId): ?Workload
{
    return Workload::where('subject_id', $subjectId)
        ->where('academic_year_id', $academicYearId)
        ->where('is_potok', true)
        ->where('workload_type', 'lecture_only')
        ->whereHas('groups', function ($query) use ($groupIds) {
            $query->whereIn('groups.id', $groupIds);
        })
        ->first();
}
}
