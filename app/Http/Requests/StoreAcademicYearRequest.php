<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('academic-years.create') 
            || auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',  // ✅ is_current → is_active
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O\'quv yili nomi kiritilishi shart',
            'name.unique' => 'Bu nomli o\'quv yili allaqachon mavjud',
            'start_date.required' => 'Boshlanish sanasi kiritilishi shart',
            'end_date.required' => 'Tugash sanasi kiritilishi shart',
            'end_date.after' => 'Tugash sanasi boshlanish sanasidan kechroq bo\'lishi kerak',
        ];
    }
}