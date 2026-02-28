<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'due_date', 'period_label', 'base_amount',
        'extra_charges', 'penalty_amount', 'discount_amount', 'final_amount',
        'paid_amount', 'status', 'paid_at', 'receipt_number', 'notes',
    ];

    protected $casts = [
        'due_date'       => 'date',
        'base_amount'    => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'discount_amount'=> 'decimal:2',
        'final_amount'   => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'extra_charges'  => 'array',
        'paid_at'        => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeDue($query)
    {
        return $query->whereIn('status', ['due', 'partial']);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->final_amount - $this->paid_amount);
    }
}
