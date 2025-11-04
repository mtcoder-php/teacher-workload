<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class SystemActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->name === 'admin';
    }

    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                'in:cache_clear,config_cache,route_cache,view_cache,optimize,clear_all'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Amal tanlanishi kerak',
            'action.string' => 'Amal matn formatida bo\'lishi kerak',
            'action.in' => 'Noto\'g\'ri amal tanlandi',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Sizda tizim amallarini bajarish huquqi yo\'q');
    }
}