<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'unit_id'     => 'required|exists:units,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'reported_by' => 'nullable|string|max:255',
            'description' => 'required|string',
            'status'      => 'sometimes|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'cost'        => 'nullable|numeric|min:0',
        ];
    }
}
