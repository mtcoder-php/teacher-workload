<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        $setting = $this->route('setting');

        if (!$setting) {
            return ['value' => 'required'];
        }

        return match($setting->type) {
            'number' => [
                'value' => 'required|numeric|min:0',
            ],
            'boolean' => [
                'value' => 'required|in:0,1,true,false',
            ],
            'json' => [
                'value' => 'nullable|json',
            ],
            'text' => [
                'value' => 'required|string|max:1000',
            ],
            default => [
                'value' => 'required|string|max:1000',
            ],
        };
    }

    public function messages(): array
    {
        return [
            'value.required' => 'Qiymat kiritish majburiy',
            'value.numeric' => 'Qiymat raqam bo\'lishi kerak',
            'value.min' => 'Qiymat 0 dan katta bo\'lishi kerak',
            'value.in' => 'Qiymat 0 yoki 1 bo\'lishi kerak',
            'value.json' => 'Qiymat to\'g\'ri JSON formatda bo\'lishi kerak',
            'value.string' => 'Qiymat matn bo\'lishi kerak',
            'value.max' => 'Qiymat :max belgidan oshmasligi kerak',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Sizda sozlamalarni o\'zgartirish huquqi yo\'q');
    }
}