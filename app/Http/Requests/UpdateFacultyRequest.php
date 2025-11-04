<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faculties')->ignore($this->route('faculty'))
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('faculties')->ignore($this->route('faculty'))
            ],
            'dean_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Fakultet nomi kiritilishi shart',
            'name.unique' => 'Bu nom allaqachon mavjud',
            'code.required' => 'Fakultet kodi kiritilishi shart',
            'code.unique' => 'Bu kod allaqachon mavjud',
            'dean_id.exists' => 'Tanlangan dekan topilmadi',
        ];
    }
    
    protected function prepareForValidation(): void
    {
        if ($this->dean_id === '' || $this->dean_id === 'null') {
            $this->merge(['dean_id' => null]);
        }
        
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => false]);
        }
    }
}