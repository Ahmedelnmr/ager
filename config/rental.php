<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rental System Configuration
    |--------------------------------------------------------------------------
    */

    // Days before contract end to trigger ending notification
    'contract_ending_notify_days' => env('CONTRACT_ENDING_NOTIFY_DAYS', 30),

    // Default currency symbol
    'currency' => env('RENTAL_CURRENCY', 'ريال'),

    // Default late penalty (used if building and contract have no setting)
    'default_late_penalty_type'  => 'none',
    'default_late_penalty_value' => 0,
];
