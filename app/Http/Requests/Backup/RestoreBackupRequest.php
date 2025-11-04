<?php

namespace App\Http\Requests\Backup;

use Illuminate\Foundation\Http\FormRequest;

class RestoreBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'confirm.required' => 'Tasdiqlash majburiy',
            'confirm.accepted' => 'Backupni tiklashni tasdiqlashingiz kerak',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Sizda backup tiklash huquqi yo\'q');
    }
}