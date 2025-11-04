<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('departments.create');
    }

    public function rules(): array
    {
        return [
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
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
            'code.unique' => 'Bu kod allaqachon mavjud',
            'head_id.exists' => 'Tanlangan mudir topilmadi',
        ];
    }
}