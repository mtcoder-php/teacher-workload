<?php

namespace App\Http\Requests;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ruxsatlarni tekshirish uchun Policy'dan foydalanish kerak.
        // Misol: return $this->user()->can('update', $this->workload);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Asosiy ma'lumotlar (agar o'zgartirilsa)
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'teacher_id' => 'sometimes|required|exists:teachers,id',

            // ✅ O'ZGARTIRILDI: `group_id` o'rniga `group_ids`
            'group_ids' => 'sometimes|required|array|min:1',
            'group_ids.*' => 'required|integer|exists:groups,id',

            // Potok uchun ixtiyoriy maydon
            'potok_code' => 'nullable|string|max:50',

            // Soatlar (agar o'zgartirilsa)
            'semester_1_lecture' => 'sometimes|nullable|numeric|min:0',
            'semester_1_practical' => 'sometimes|nullable|numeric|min:0',
            'semester_1_laboratory' => 'sometimes|nullable|numeric|min:0',
            'semester_1_seminar' => 'sometimes|nullable|numeric|min:0',
            // ... boshqa barcha soat maydonlari uchun ham xuddi shunday ...
            'semester_2_lecture' => 'sometimes|nullable|numeric|min:0',
            'semester_2_practical' => 'sometimes|nullable|numeric|min:0',
            'semester_2_laboratory' => 'sometimes|nullable|numeric|min:0',
            'semester_2_seminar' => 'sometimes|nullable|numeric|min:0',

            // Status va izohlar
            'status' => 'sometimes|required|in:draft,pending,confirmed,completed',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Agar guruhlar o'zgartirilgan bo'lsa, 'is_potok' holatini qayta hisoblash
        if ($this->has('group_ids')) {
            $isPotok = is_array($this->group_ids) && count($this->group_ids) > 1;
            $this->merge([
                'is_potok' => $isPotok,
            ]);

            // Agar guruhlar o'zgargan bo'lsa, yo'nalishni ham qayta aniqlash
            if (!empty($this->group_ids)) {
                $firstGroup = Group::find($this->group_ids[0]);
                if ($firstGroup) {
                    $this->merge(['direction_id' => $firstGroup->direction_id]);
                }
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // Agar guruhlar o'zgartirilayotgan bo'lsa, takroriylikni tekshirish
            if ($this->has('group_ids')) {
                // Bu yerga ham xuddi StoreWorkloadRequest'dagi kabi
                // takroriy yuklama mavjudligini tekshiradigan mantiq qo'shish mumkin,
                // ammo bu safar joriy workload ID'sini istisno qilish kerak bo'ladi.
                // Misol: ->where('id', '!=', $this->workload->id)
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            '*.required' => 'Bu maydon to\'ldirilishi shart',
            '*.exists' => 'Tanlangan qiymat mavjud emas',
            'group_ids.required' => 'Kamida bitta guruh tanlanishi kerak.',
            'group_ids.min' => 'Kamida bitta guruh tanlanishi kerak.',
        ];
    }
}
