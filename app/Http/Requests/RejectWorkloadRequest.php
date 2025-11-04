<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ========== REJECT WORKLOAD REQUEST ==========
 * Yuklamani rad etish uchun validation
 */
class RejectWorkloadRequest extends FormRequest
{
    /**
     * Foydalanuvchining huquqini tekshirish
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('workloads.approve');
    }

    /**
     * Validation qaydlari
     */
    public function rules(): array
    {
        return [
            'comment' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
        ];
    }

    /**
     * Validation xato xabarlari
     */
    public function messages(): array
    {
        return [
            'comment.required' => 'Rad etish sababi kiritilishi majburiy',
            'comment.string' => 'Sabab matn bo\'lishi kerak',
            'comment.min' => 'Sabab kamida 10 ta belgidan iborat bo\'lishi kerak',
            'comment.max' => 'Sabab maksimal 500 belgidan iborat bo\'lishi mumkin',
        ];
    }

    /**
     * Custom attributes
     */
    public function attributes(): array
    {
        return [
            'comment' => 'Rad etish sababi',
        ];
    }
}

?>