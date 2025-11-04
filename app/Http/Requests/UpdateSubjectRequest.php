<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('subjects.edit');
    }

    public function rules(): array
    {
        return [
            // Asosiy ma'lumotlar
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')->ignore($this->subject),
            ],
            'department_id' => 'required|exists:departments,id',
            'direction_id' => 'nullable|exists:directions,id',
            'course_level' => 'required|integer|min:1|max:4',
            'credit_hours' => 'required|integer|min:1|max:10',
            
            // 1-semestr soatlari
            'semester_1_lecture' => 'required|numeric|min:0|max:500',
            'semester_1_practical' => 'required|numeric|min:0|max:500',
            'semester_1_laboratory' => 'required|numeric|min:0|max:500',
            'semester_1_seminar' => 'required|numeric|min:0|max:500',
            'semester_1_practice' => 'required|numeric|min:0|max:500',
            'semester_1_exam' => 'required|numeric|min:0|max:100',
            'semester_1_test' => 'required|numeric|min:0|max:100',
            
            // 2-semestr soatlari
            'semester_2_lecture' => 'required|numeric|min:0|max:500',
            'semester_2_practical' => 'required|numeric|min:0|max:500',
            'semester_2_laboratory' => 'required|numeric|min:0|max:500',
            'semester_2_seminar' => 'required|numeric|min:0|max:500',
            'semester_2_practice' => 'required|numeric|min:0|max:500',
            'semester_2_exam' => 'required|numeric|min:0|max:100',
            'semester_2_test' => 'required|numeric|min:0|max:100',
            
            // Qo'shimcha soatlar
            'coursework_hours' => 'nullable|numeric|min:0|max:200',
            'diploma_hours' => 'nullable|numeric|min:0|max:200',
            'consultation_hours' => 'nullable|numeric|min:0|max:200',
            
            // Fan turlari
            'subject_type' => ['required', Rule::in(['asosiy', 'yordamchi', 'ixtiyoriy'])],
            'education_form' => ['nullable', Rule::in(['kunduzgi', 'kechki', 'sirtqi'])],
            
            // Patok
            'can_be_potok' => 'boolean',
            'min_groups_for_potok' => 'required_if:can_be_potok,true|nullable|integer|min:2|max:10',
            
            // Nazorat turlari
            'semester_1_control' => ['nullable', Rule::in(['imtihon', 'test', 'baholash'])],
            'semester_2_control' => ['nullable', Rule::in(['imtihon', 'test', 'baholash'])],
            
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Fan nomi kiritilishi shart',
            'code.required' => 'Fan kodi kiritilishi shart',
            'code.unique' => 'Bu kod allaqachon mavjud',
            'department_id.required' => 'Kafedra tanlanishi shart',
            'course_level.required' => 'Kurs darajasi tanlanishi shart',
            'credit_hours.required' => 'Kredit soat kiritilishi shart',
            'credit_hours.min' => 'Kredit soat kamida 1 bo\'lishi kerak',
            'subject_type.required' => 'Fan turi tanlanishi shart',
            'min_groups_for_potok.required_if' => 'Patok uchun minimal guruhlar soni kiritilishi shart',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Fan nomi',
            'code' => 'Fan kodi',
            'department_id' => 'Kafedra',
            'direction_id' => 'Yo\'nalish',
            'course_level' => 'Kurs darajasi',
            'credit_hours' => 'Kredit soat',
            'subject_type' => 'Fan turi',
            'education_form' => 'Ta\'lim shakli',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kamida bitta semestrda soat bo'lishi kerak
            $semester1Total = 
                ($this->semester_1_lecture ?? 0) +
                ($this->semester_1_practical ?? 0) +
                ($this->semester_1_laboratory ?? 0) +
                ($this->semester_1_seminar ?? 0) +
                ($this->semester_1_practice ?? 0);
                
            $semester2Total = 
                ($this->semester_2_lecture ?? 0) +
                ($this->semester_2_practical ?? 0) +
                ($this->semester_2_laboratory ?? 0) +
                ($this->semester_2_seminar ?? 0) +
                ($this->semester_2_practice ?? 0);
            
            if ($semester1Total == 0 && $semester2Total == 0) {
                $validator->errors()->add(
                    'semester_1_lecture',
                    'Kamida bitta semestrda dars soati kiritilishi kerak'
                );
            }
        });
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'coursework_hours' => $this->coursework_hours ?? 0,
            'diploma_hours' => $this->diploma_hours ?? 0,
            'consultation_hours' => $this->consultation_hours ?? 0,
        ]);
    }
}