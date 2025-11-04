<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('faculties.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:faculties,name',
            'code' => 'required|string|max:50|unique:faculties,code',
            'dean_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Fakultet nomi kiritilishi shart',
            'name.unique' => 'Bu nom bilan fakultet allaqachon mavjud',
            'code.required' => 'Fakultet kodi kiritilishi shart',
            'code.unique' => 'Bu kod allaqachon mavjud',
            'dean_id.exists' => 'Tanlangan dekan topilmadi',
        ];
    }
}