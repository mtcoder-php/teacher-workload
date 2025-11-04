<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('departments.edit');
    }

    public function rules(): array
    {
        return [
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($this->department->id)
            ],
            'head_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'faculty_id.required' => 'Fakultet tanlanishi shart',
            'faculty_id.exists' => 'Tanlangan fakultet topilmadi',
            'name.required' => 'Kafedra nomi kiritilishi shart',
            'name.max' => 'Kafedra nomi 255 belgidan oshmasligi kerak',
            'code.required' => 'Kafedra kodi kiritilishi shart',
            'code.max' => 'Kafedra kodi 50 belgidan oshmasligi kerak',
            'code.unique' => 'Bu kod allaqachon mavjud',
            'head_id.exists' => 'Tanlangan mudir topilmadi',
            'is_active.boolean' => 'Faollik holati noto\'g\'ri formatda',
        ];
    }
}