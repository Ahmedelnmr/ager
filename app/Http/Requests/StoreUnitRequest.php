<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'building_id'  => 'required|exists:buildings,id',
            'unit_number'  => 'required|string|max:50',
            'floor'        => 'nullable|string|max:20',
            'type'         => 'required|in:residential,commercial,office',
            'size'         => 'nullable|numeric|min:0',
            'status'       => 'required|in:vacant,rented,maintenance',
            'base_rent'    => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
        ];
    }
}
