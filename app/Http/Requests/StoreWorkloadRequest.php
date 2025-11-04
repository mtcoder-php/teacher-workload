<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'direction_id' => 'required|exists:directions,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',

            // Potok
            'is_potok' => 'boolean',
            'potok_code' => 'nullable|string|max:50',

            'notes' => 'nullable|string|max:1000',
            'rating' => 'nullable|numeric|min:0',

            // 1-semestr
            'semester_1_lecture' => 'nullable|numeric|min:0',
            'semester_1_practical' => 'nullable|numeric|min:0',
            'semester_1_laboratory' => 'nullable|numeric|min:0',
            'semester_1_seminar' => 'nullable|numeric|min:0',
            'semester_1_practice' => 'nullable|numeric|min:0',
            'semester_1_exam' => 'nullable|numeric|min:0',
            'semester_1_test' => 'nullable|numeric|min:0',

            // 2-semestr
            'semester_2_lecture' => 'nullable|numeric|min:0',
            'semester_2_practical' => 'nullable|numeric|min:0',
            'semester_2_laboratory' => 'nullable|numeric|min:0',
            'semester_2_seminar' => 'nullable|numeric|min:0',
            'semester_2_practice' => 'nullable|numeric|min:0',
            'semester_2_exam' => 'nullable|numeric|min:0',
            'semester_2_test' => 'nullable|numeric|min:0',

            // Umumiy
            'coursework_hours' => 'nullable|numeric|min:0',
            'diploma_hours' => 'nullable|numeric|min:0',
            'consultation_hours' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Kafedra tanlanishi shart',
            'direction_id.required' => 'Yo\'nalish tanlanishi shart',
            'subject_id.required' => 'Fan tanlanishi shart',
            'teacher_id.required' => 'O\'qituvchi tanlanishi shart',
            'group_ids.required' => 'Kamida bitta guruh tanlanishi shart',
            'group_ids.min' => 'Kamida bitta guruh tanlang',
        ];
    }
}
