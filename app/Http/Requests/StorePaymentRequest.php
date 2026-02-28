<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'required|in:cash,transfer,cheque',
            'payment_date'    => 'required|date',
            'transaction_ref' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ];
    }
}
