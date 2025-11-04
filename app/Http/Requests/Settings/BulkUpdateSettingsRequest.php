<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'settings' => 'required|array|min:1',
            'settings.*.id' => 'required|integer|exists:settings,id',
            'settings.*.value' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'settings.required' => 'Sozlamalar ro\'yxati bo\'sh bo\'lmasligi kerak',
            'settings.array' => 'Sozlamalar array formatida bo\'lishi kerak',
            'settings.min' => 'Kamida 1 ta sozlama bo\'lishi kerak',
            'settings.*.id.required' => 'Sozlama ID si majburiy',
            'settings.*.id.integer' => 'Sozlama ID si raqam bo\'lishi kerak',
            'settings.*.id.exists' => 'Sozlama topilmadi',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Sizda sozlamalarni o\'zgartirish huquqi yo\'q');
    }
}