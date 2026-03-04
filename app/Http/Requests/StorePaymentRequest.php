<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $schedule = $this->route('schedule');
        $maxAmount = $schedule ? $schedule->remaining_amount : 99999999;

        return [
            'amount'          => ['required', 'numeric', 'min:0.01', "max:{$maxAmount}"],
            'payment_method'  => 'required|in:cash,transfer,cheque',
            'payment_date'    => 'required|date',
            'transaction_ref' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ];
    }
    
    public function messages(): array
    {
        return [
            'amount.max' => 'عذراً القيمة المدخلة أكبر من المبلغ المتبقي للاستحقاق.',
        ];
    }
}
