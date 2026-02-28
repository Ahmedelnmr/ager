<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('tenant')?->id;
        return [
            'name'        => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20|unique:tenants,national_id,' . $id,
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:500',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
