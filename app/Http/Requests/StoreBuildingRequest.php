<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'address'  => 'nullable|string|max:500',
            'city'     => 'nullable|string|max:100',
            'notes'    => 'nullable|string',
            'settings' => 'nullable|array',
            'settings.late_penalty_type'  => 'nullable|in:none,percent,fixed',
            'settings.late_penalty_value' => 'nullable|numeric|min:0',
            'settings.annual_increase_default' => 'nullable|numeric|min:0',
            'settings.currency'           => 'nullable|string|max:5',
        ];
    }
}
