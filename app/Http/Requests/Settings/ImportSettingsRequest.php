<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ImportSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:json|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Fayl tanlash majburiy',
            'file.file' => 'Fayl yuklash kerak',
            'file.mimes' => 'Faqat JSON fayl ruxsat etilgan',
            'file.max' => 'Fayl hajmi 1MB dan oshmasligi kerak',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Sizda sozlamalarni import qilish huquqi yo\'q');
    }
}