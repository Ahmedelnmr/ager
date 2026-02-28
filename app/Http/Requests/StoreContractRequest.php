<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'unit_id'                  => 'required|exists:units,id',
            'tenant_id'                => 'required|exists:tenants,id',
            'start_date'               => 'required|date',
            'end_date'                 => 'required|date|after:start_date',
            'base_rent'                => 'required|numeric|min:0',
            'payment_cycle'            => 'required|in:monthly,quarterly,yearly',
            'due_day'                  => 'nullable|integer|min:1|max:31',
            'security_deposit_amount'  => 'nullable|numeric|min:0',
            'deposit_policy'           => 'required|in:refundable,deduct_last_month,non_refundable,partial',
            'annual_increase_type'     => 'required|in:none,percent,fixed',
            'annual_increase_value'    => 'nullable|numeric|min:0',
            'late_penalty_type'        => 'required|in:none,percent,fixed',
            'late_penalty_value'       => 'nullable|numeric|min:0',
            'early_termination_policy' => 'nullable|string',
            'notes'                    => 'nullable|string',
            'contract_file'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status'                   => 'sometimes|in:active,expired,terminated',
        ];
    }
}
