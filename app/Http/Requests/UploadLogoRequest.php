<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Logo fayli tanlash majburiy',
            'logo.image' => 'Faqat rasm fayllarini yuklash mumkin',
            'logo.mimes' => 'Faqat jpeg, png, jpg, svg formatlarida bo\'lishi kerak',
            'logo.max' => 'Fayl hajmi 2MB dan oshmasligi kerak',
        ];
    }
}