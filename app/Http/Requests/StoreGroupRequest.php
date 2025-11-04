<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('groups', 'code')
            ],
            'direction_id' => ['required', 'exists:directions,id'],
            'course' => ['required', 'integer', 'min:1', 'max:6'],
            'education_type' => ['required', 'string', 'in:kunduzgi,sirtqi,kechki,masofaviy'],
            'education_language' => ['required', 'string', 'in:uzbek,russian'], // ✅ QOSHILDI
            'student_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Guruh nomi kiritilishi shart',
            'name.max' => 'Guruh nomi 100 belgidan oshmasligi kerak',
            'code.required' => 'Guruh kodi kiritilishi shart',
            'code.unique' => 'Bu kod allaqachon mavjud',
            'code.max' => 'Guruh kodi 50 belgidan oshmasligi kerak',
            'direction_id.required' => 'Yo\'nalish tanlanishi shart',
            'direction_id.exists' => 'Tanlangan yo\'nalish topilmadi',
            'course.required' => 'Kurs tanlanishi shart',
            'course.integer' => 'Kurs raqam bo\'lishi kerak',
            'course.min' => 'Kurs 1 dan kam bo\'lmasligi kerak',
            'course.max' => 'Kurs 6 dan oshmasligi kerak',
            'education_type.required' => 'Ta\'lim shakli tanlanishi shart',
            'education_type.in' => 'Tanlangan ta\'lim shakli noto\'g\'ri',
            'education_language.required' => 'Ta\'lim tili tanlanishi shart', // ✅ QOSHILDI
            'education_language.in' => 'Tanlangan ta\'lim tili noto\'g\'ri', // ✅ QOSHILDI
            'student_count.integer' => 'Talabalar soni butun son bo\'lishi kerak',
            'student_count.min' => 'Talabalar soni manfiy bo\'lishi mumkin emas',
            'student_count.max' => 'Talabalar soni 100 dan oshmasligi kerak',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Guruh nomi',
            'code' => 'Guruh kodi',
            'direction_id' => 'Yo\'nalish',
            'course' => 'Kurs',
            'education_type' => 'Ta\'lim shakli',
            'education_language' => 'Ta\'lim tili', // ✅ QOSHILDI
            'student_count' => 'Talabalar soni',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
        
        // ✅ Default qiymat
        if (!$this->has('education_language')) {
            $this->merge(['education_language' => 'uzbek']);
        }
    }
}