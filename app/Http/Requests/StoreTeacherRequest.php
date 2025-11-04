<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('teachers.create');
    }

    public function rules(): array
    {
        return [
            // User ma'lumotlari
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            
            // Teacher ma'lumotlari
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'academic_degree' => 'nullable|string|max:100',
            'academic_title' => 'nullable|string|max:100',
            'employment_type' => 'required|in:main_job,internal_part_time,internal_additional,external_part_time,hourly', // ✅ YANGILANDI
            'hire_date' => 'nullable|date|before_or_equal:today',
            'birth_date' => 'nullable|date|before:today',
            'passport_serial' => 'nullable|string|max:20',
            'inn' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism-familiya kiritilishi shart',
            'email.required' => 'Email kiritilishi shart',
            'email.email' => 'Email formati noto\'g\'ri',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiritilishi shart',
            'password.min' => 'Parol kamida 8 belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'department_id.required' => 'Kafedra tanlanishi shart',
            'department_id.exists' => 'Tanlangan kafedra topilmadi',
            'employment_type.required' => 'Bandlik turi tanlanishi shart',
            'employment_type.in' => 'Bandlik turi noto\'g\'ri', // ✅ YANGILANDI
            'hire_date.before_or_equal' => 'Ishga qabul qilish sanasi bugungi kundan katta bo\'lmasligi kerak',
            'birth_date.before' => 'Tug\'ilgan sana bugungi kundan oldin bo\'lishi kerak',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Ism-familiya',
            'email' => 'Email',
            'password' => 'Parol',
            'phone' => 'Telefon',
            'department_id' => 'Kafedra',
            'position' => 'Lavozim',
            'academic_degree' => 'Ilmiy daraja',
            'academic_title' => 'Ilmiy unvon',
            'employment_type' => 'Bandlik turi',
            'hire_date' => 'Ishga qabul qilish sanasi',
            'birth_date' => 'Tug\'ilgan sana',
        ];
    }
}