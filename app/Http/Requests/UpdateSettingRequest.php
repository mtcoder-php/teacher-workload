<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Faqat admin ruxsat berish
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        $setting = $this->route('setting');

        $rules = [
            'value' => 'required',
        ];

        // Type bo'yicha qo'shimcha validation
        if ($setting) {
            switch ($setting->type) {
                case 'number':
                    $rules['value'] = 'required|numeric';
                    break;
                case 'boolean':
                    $rules['value'] = 'required|boolean';
                    break;
                case 'json':
                    $rules['value'] = 'required|json';
                    break;
                case 'text':
                default:
                    $rules['value'] = 'required|string|max:1000';
                    break;
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'value.required' => 'Qiymat kiritish majburiy',
            'value.numeric' => 'Qiymat raqam bo\'lishi kerak',
            'value.boolean' => 'Qiymat true yoki false bo\'lishi kerak',
            'value.json' => 'Qiymat to\'g\'ri JSON formatda bo\'lishi kerak',
            'value.string' => 'Qiymat matn bo\'lishi kerak',
            'value.max' => 'Qiymat 1000 belgidan oshmasligi kerak',
        ];
    }
}