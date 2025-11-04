<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('subjects.create');
    }

    public function rules(): array
    {
        return [
            // Asosiy ma'lumotlar
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
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
            // Asosiy ma'lumotlar
            'name.required' => 'Fan nomi kiritilishi shart',
            'name.max' => 'Fan nomi 255 belgidan oshmasligi kerak',
            'code.required' => 'Fan kodi kiritilishi shart',
            'code.unique' => 'Bu kod allaqachon mavjud',
            'code.max' => 'Fan kodi 50 belgidan oshmasligi kerak',
            'department_id.required' => 'Kafedra tanlanishi shart',
            'department_id.exists' => 'Tanlangan kafedra topilmadi',
            'direction_id.exists' => 'Tanlangan yo\'nalish topilmadi',
            'course_level.required' => 'Kurs darajasi tanlanishi shart',
            'course_level.min' => 'Kurs darajasi 1 dan kam bo\'lmasligi kerak',
            'course_level.max' => 'Kurs darajasi 4 dan oshmasligi kerak',
            'credit_hours.required' => 'Kredit soat kiritilishi shart',
            'credit_hours.integer' => 'Kredit soat butun son bo\'lishi kerak',
            'credit_hours.min' => 'Kredit soat kamida 1 bo\'lishi kerak',
            'credit_hours.max' => 'Kredit soat 10 dan oshmasligi kerak',
            
            // 1-semestr
            'semester_1_lecture.required' => '1-semestr ma\'ruza soati kiritilishi shart',
            'semester_1_lecture.min' => 'Soat 0 dan kam bo\'lmasligi kerak',
            'semester_1_lecture.max' => 'Soat 500 dan oshmasligi kerak',
            'semester_1_practical.required' => '1-semestr amaliy mashg\'ulot soati kiritilishi shart',
            'semester_1_laboratory.required' => '1-semestr laboratoriya soati kiritilishi shart',
            'semester_1_seminar.required' => '1-semestr seminar soati kiritilishi shart',
            'semester_1_practice.required' => '1-semestr amaliyot soati kiritilishi shart',
            'semester_1_exam.required' => '1-semestr imtihon soati kiritilishi shart',
            'semester_1_test.required' => '1-semestr sinov soati kiritilishi shart',
            
            // 2-semestr
            'semester_2_lecture.required' => '2-semestr ma\'ruza soati kiritilishi shart',
            'semester_2_practical.required' => '2-semestr amaliy mashg\'ulot soati kiritilishi shart',
            'semester_2_laboratory.required' => '2-semestr laboratoriya soati kiritilishi shart',
            'semester_2_seminar.required' => '2-semestr seminar soati kiritilishi shart',
            'semester_2_practice.required' => '2-semestr amaliyot soati kiritilishi shart',
            'semester_2_exam.required' => '2-semestr imtihon soati kiritilishi shart',
            'semester_2_test.required' => '2-semestr sinov soati kiritilishi shart',
            
            // Qo'shimcha
            'coursework_hours.min' => 'Kurs ishi soati 0 dan kam bo\'lmasligi kerak',
            'coursework_hours.max' => 'Kurs ishi soati 200 dan oshmasligi kerak',
            'diploma_hours.min' => 'Diplom ishi soati 0 dan kam bo\'lmasligi kerak',
            'diploma_hours.max' => 'Diplom ishi soati 200 dan oshmasligi kerak',
            'consultation_hours.min' => 'Konsultatsiya soati 0 dan kam bo\'lmasligi kerak',
            'consultation_hours.max' => 'Konsultatsiya soati 200 dan oshmasligi kerak',
            
            // Turlar
            'subject_type.required' => 'Fan turi tanlanishi shart',
            'subject_type.in' => 'Fan turi noto\'g\'ri',
            'education_form.in' => 'Ta\'lim shakli noto\'g\'ri',
            
            // Patok
            'min_groups_for_potok.required_if' => 'Patok uchun minimal guruhlar soni kiritilishi shart',
            'min_groups_for_potok.min' => 'Minimal guruhlar soni kamida 2 bo\'lishi kerak',
            'min_groups_for_potok.max' => 'Minimal guruhlar soni 10 dan oshmasligi kerak',
            
            // Nazorat
            'semester_1_control.in' => '1-semestr nazorat turi noto\'g\'ri',
            'semester_2_control.in' => '2-semestr nazorat turi noto\'g\'ri',
            
            'description.max' => 'Tavsif 1000 belgidan oshmasligi kerak',
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
            
            'semester_1_lecture' => '1-semestr ma\'ruza',
            'semester_1_practical' => '1-semestr amaliy',
            'semester_1_laboratory' => '1-semestr laboratoriya',
            'semester_1_seminar' => '1-semestr seminar',
            'semester_1_practice' => '1-semestr amaliyot',
            'semester_1_exam' => '1-semestr imtihon',
            'semester_1_test' => '1-semestr sinov',
            
            'semester_2_lecture' => '2-semestr ma\'ruza',
            'semester_2_practical' => '2-semestr amaliy',
            'semester_2_laboratory' => '2-semestr laboratoriya',
            'semester_2_seminar' => '2-semestr seminar',
            'semester_2_practice' => '2-semestr amaliyot',
            'semester_2_exam' => '2-semestr imtihon',
            'semester_2_test' => '2-semestr sinov',
            
            'coursework_hours' => 'Kurs ishi',
            'diploma_hours' => 'Diplom ishi',
            'consultation_hours' => 'Konsultatsiya',
            
            'subject_type' => 'Fan turi',
            'education_form' => 'Ta\'lim shakli',
            'can_be_potok' => 'Patok imkoniyati',
            'min_groups_for_potok' => 'Minimal guruhlar soni',
            
            'semester_1_control' => '1-semestr nazorat',
            'semester_2_control' => '2-semestr nazorat',
            
            'description' => 'Tavsif',
        ];
    }

    /**
     * Qo'shimcha validatsiyadan keyin
     */
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
            
            // Agar patok bo'lsa, min_groups_for_potok majburiy
            if ($this->can_be_potok && empty($this->min_groups_for_potok)) {
                $validator->errors()->add(
                    'min_groups_for_potok',
                    'Patok rejimi yoqilganda minimal guruhlar soni kiritilishi shart'
                );
            }
        });
    }

    /**
     * Ma'lumotlarni tozalash
     */
    protected function prepareForValidation()
    {
        // Agar can_be_potok false bo'lsa, min_groups_for_potok ni null qilish
        if (!$this->can_be_potok) {
            $this->merge([
                'min_groups_for_potok' => 2, // default
            ]);
        }

        // Agar qo'shimcha soatlar bo'sh bo'lsa, 0 qilish
        $this->merge([
            'coursework_hours' => $this->coursework_hours ?? 0,
            'diploma_hours' => $this->diploma_hours ?? 0,
            'consultation_hours' => $this->consultation_hours ?? 0,
        ]);
    }
}