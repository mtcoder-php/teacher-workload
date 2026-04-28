<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Workload;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // ─── Step 1 ───────────────────────────────────────────────────────
            'department_id'   => 'required|exists:departments,id',
            'direction_ids'   => 'nullable|array',
            'direction_ids.*' => 'exists:directions,id',
            'direction_id'    => 'nullable|exists:directions,id',
            'course'          => 'nullable|integer|min:1|max:6',
            'is_potok'        => 'required|boolean',

            // ─── Step 2 ───────────────────────────────────────────────────────
            'group_ids'       => 'required|array|min:1',
            'group_ids.*'     => 'exists:groups,id',

            // ─── Step 3 ───────────────────────────────────────────────────────
            'subject_id'        => 'required|exists:subjects,id',
            'academic_year_id'  => 'nullable|exists:academic_years,id',

            'semester_1_lecture'    => 'required|numeric|min:0',
            'semester_1_practical'  => 'required|numeric|min:0',
            'semester_1_laboratory' => 'required|numeric|min:0',
            'semester_1_seminar'    => 'required|numeric|min:0',
            'semester_1_practice'   => 'required|numeric|min:0',
            'semester_1_exam'       => 'required|numeric|min:0',
            'semester_1_test'       => 'required|numeric|min:0',

            'semester_2_lecture'    => 'required|numeric|min:0',
            'semester_2_practical'  => 'required|numeric|min:0',
            'semester_2_laboratory' => 'required|numeric|min:0',
            'semester_2_seminar'    => 'required|numeric|min:0',
            'semester_2_practice'   => 'required|numeric|min:0',
            'semester_2_exam'       => 'required|numeric|min:0',
            'semester_2_test'       => 'required|numeric|min:0',

            'coursework_hours'   => 'nullable|numeric|min:0',
            'diploma_hours'      => 'nullable|numeric|min:0',
            'consultation_hours' => 'nullable|numeric|min:0',

            'has_rating' => 'nullable|boolean',   // ← QO'SHILDI

            // ─── Step 4 ───────────────────────────────────────────────────────
            'teacher_id' => 'required|exists:teachers,id',
            'notes'      => 'nullable|string|max:1000',

            'status' => 'nullable|in:draft,pending,approved',
        ];
    }

    /**
     * Qo'shimcha server-side validatsiya
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            // Academic year
            $academicYearId = $this->academic_year_id
                ?? AcademicYear::where('is_active', true)->value('id');

            if (!$academicYearId) {
                $v->errors()->add('academic_year_id', 'Faol o\'quv yili topilmadi');
                return;
            }

            // Potok validatsiyasi
            $groupCount = count($this->group_ids ?? []);
            if ($this->boolean('is_potok') && $groupCount < 2) {
                $v->errors()->add('group_ids', 'Potokli holatda kamida 2 ta guruh tanlang');
            }
            if ($this->boolean('is_potok') && $groupCount > 8) {
                $v->errors()->add('group_ids', 'Potokli holatda maksimal 8 ta guruh tanlash mumkin');
            }


            // Kamida 1 soat kiritilishi kerak
            $hourFields = [
                'semester_1_lecture','semester_1_practical','semester_1_laboratory',
                'semester_1_seminar','semester_1_practice','semester_1_exam','semester_1_test',
                'semester_2_lecture','semester_2_practical','semester_2_laboratory',
                'semester_2_seminar','semester_2_practice','semester_2_exam','semester_2_test',
            ];
            $totalHours = array_sum(array_map(fn($f) => floatval($this->input($f, 0)), $hourFields));
            if ($totalHours <= 0) {
                $v->errors()->add('semester_1_lecture', 'Kamida bitta soat kiritilishi shart');
                return;
            }

            // Fan soatlari limitini tekshirish
            $subject = Subject::find($this->subject_id);
            if (!$subject) return;

            $groupIds = $this->input('group_ids', []);

            // Ma'ruza: GLOBAL (barcha guruhlar bo'yicha)
            $lectureDist = Workload::where('subject_id', $this->subject_id)
                ->where('academic_year_id', $academicYearId)
                ->selectRaw('
                    COALESCE(SUM(semester_1_lecture),0) as s1_lec,
                    COALESCE(SUM(semester_2_lecture),0) as s2_lec
                ')->first();

            // Amaliy: faqat SHU guruhlar bo'yicha, lecture_only bo'lmagan
            $practiceQuery = Workload::where('subject_id', $this->subject_id)
                ->where('academic_year_id', $academicYearId)
                ->where(fn($q) => $q->where('is_potok', false)
                    ->orWhere('workload_type', '!=', 'lecture_only'));
            if (!empty($groupIds)) {
                $practiceQuery->whereHas('groups', fn($g) => $g->whereIn('groups.id', $groupIds));
            }
            $practiceDist = $practiceQuery->selectRaw('
                COALESCE(SUM(semester_1_practical),0)  as s1_pra,
                COALESCE(SUM(semester_1_laboratory),0) as s1_lab,
                COALESCE(SUM(semester_1_seminar),0)    as s1_sem,
                COALESCE(SUM(semester_1_practice),0)   as s1_prc,
                COALESCE(SUM(semester_1_exam),0)       as s1_ex,
                COALESCE(SUM(semester_1_test),0)       as s1_tst,
                COALESCE(SUM(semester_2_practical),0)  as s2_pra,
                COALESCE(SUM(semester_2_laboratory),0) as s2_lab,
                COALESCE(SUM(semester_2_seminar),0)    as s2_sem,
                COALESCE(SUM(semester_2_practice),0)   as s2_prc,
                COALESCE(SUM(semester_2_exam),0)       as s2_ex,
                COALESCE(SUM(semester_2_test),0)       as s2_tst,
                COALESCE(SUM(coursework_hours),0)      as cw,
                COALESCE(SUM(diploma_hours),0)         as dip,
                COALESCE(SUM(consultation_hours),0)    as con
            ')->first();

            $checks = [
                'semester_1_lecture'    => [$lectureDist->s1_lec,   '1-semestr ma\'ruza'],
                'semester_1_practical'  => [$practiceDist->s1_pra,  '1-semestr amaliy'],
                'semester_1_laboratory' => [$practiceDist->s1_lab,  '1-semestr laboratoriya'],
                'semester_1_seminar'    => [$practiceDist->s1_sem,  '1-semestr seminar'],
                'semester_1_practice'   => [$practiceDist->s1_prc,  '1-semestr amaliyot'],
                'semester_1_exam'       => [$practiceDist->s1_ex,   '1-semestr imtihon'],
                'semester_1_test'       => [$practiceDist->s1_tst,  '1-semestr sinov'],
                'semester_2_lecture'    => [$lectureDist->s2_lec,   '2-semestr ma\'ruza'],
                'semester_2_practical'  => [$practiceDist->s2_pra,  '2-semestr amaliy'],
                'semester_2_laboratory' => [$practiceDist->s2_lab,  '2-semestr laboratoriya'],
                'semester_2_seminar'    => [$practiceDist->s2_sem,  '2-semestr seminar'],
                'semester_2_practice'   => [$practiceDist->s2_prc,  '2-semestr amaliyot'],
                'semester_2_exam'       => [$practiceDist->s2_ex,   '2-semestr imtihon'],
                'semester_2_test'       => [$practiceDist->s2_tst,  '2-semestr sinov'],
                'coursework_hours'      => [$practiceDist->cw,      'Kurs ishi'],
                'diploma_hours'         => [$practiceDist->dip,     'Diplom ishi'],
                'consultation_hours'    => [$practiceDist->con,     'Konsultatsiya'],
            ];

            foreach ($checks as $field => [$used, $label]) {
                $max       = floatval($subject->{$field} ?? 0);
                $entered   = floatval($this->input($field, 0));
                $available = max(0, $max - floatval($used));

                if ($max > 0 && $entered > $available) {
                    $v->errors()->add(
                        $field,
                        "{$label}: limit {$available} soat, siz {$entered} soat kiritdingiz"
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Kafedra tanlanishi shart',
            'group_ids.required'     => 'Kamida bitta guruh tanlanishi shart',
            'group_ids.max'          => 'Maksimal 8 ta guruh tanlash mumkin',
            'subject_id.required'    => 'Fan tanlanishi shart',
            'teacher_id.required'    => 'O\'qituvchi tanlanishi shart',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_potok'           => $this->boolean('is_potok'),
            'has_rating'         => $this->boolean('has_rating'),   // ← QO'SHILDI
            'coursework_hours'   => $this->input('coursework_hours',   0),
            'diploma_hours'      => $this->input('diploma_hours',      0),
            'consultation_hours' => $this->input('consultation_hours', 0),
        ]);
    }
}
