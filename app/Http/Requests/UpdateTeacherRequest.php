<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('teachers.edit');
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
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
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.min' => 'Parol kamida 8 belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'department_id.required' => 'Kafedra tanlanishi shart',
            'employment_type.required' => 'Bandlik turi tanlanishi shart',
            'employment_type.in' => 'Bandlik turi noto\'g\'ri', // ✅ YANGILANDI
        ];
    }
}