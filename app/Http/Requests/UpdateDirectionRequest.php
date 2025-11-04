<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDirectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('directions', 'code')->ignore($this->direction->id)
            ],
            'department_id' => 'required|exists:departments,id',
            'degree_type' => 'required|in:bakalavr,magistratura',
            'duration_years' => 'required|integer|min:1|max:6',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Yo\'nalish nomi kiritilishi shart',
            'code.required' => 'Yo\'nalish kodi kiritilishi shart',
            'code.unique' => 'Bunday kodli yo\'nalish allaqachon mavjud',
            'department_id.required' => 'Kafedra tanlanishi shart',
            'department_id.exists' => 'Tanlangan kafedra topilmadi',
            'degree_type.required' => 'Ta\'lim darajasi tanlanishi shart',
            'degree_type.in' => 'Noto\'g\'ri ta\'lim darajasi',
            'duration_years.required' => 'O\'qish muddati kiritilishi shart',
            'duration_years.integer' => 'O\'qish muddati raqam bo\'lishi kerak',
            'duration_years.min' => 'O\'qish muddati 1 yildan kam bo\'lmasligi kerak',
            'duration_years.max' => 'O\'qish muddati 6 yildan ko\'p bo\'lmasligi kerak',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active') && is_string($this->is_active)) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
