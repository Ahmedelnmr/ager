<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unit_id', 'tenant_id', 'start_date', 'end_date', 'base_rent',
        'payment_cycle', 'due_day', 'security_deposit_amount', 'deposit_policy',
        'annual_increase_type', 'annual_increase_value',
        'late_penalty_type', 'late_penalty_value',
        'early_termination_policy', 'notes', 'file_path', 'settings', 'status',
    ];

    protected $casts = [
        'start_date'               => 'date',
        'end_date'                 => 'date',
        'base_rent'                => 'decimal:2',
        'due_day'                  => 'integer',
        'security_deposit_amount'  => 'decimal:2',
        'annual_increase_value'    => 'decimal:2',
        'late_penalty_value'       => 'decimal:2',
        'settings'                 => 'array',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rentSchedules(): HasMany
    {
        return $this->hasMany(RentSchedule::class)->orderBy('due_date');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date');
    }

    public function additionalCharges(): HasMany
    {
        return $this->hasMany(AdditionalCharge::class);
    }

    /**
     * Resolve a setting: contract.settings > building.settings > $default
     */
    public function resolveSetting(string $key, mixed $default = null): mixed
    {
        $contractVal = data_get($this->settings, $key);
        if ($contractVal !== null) {
            return $contractVal;
        }
        $building = $this->unit?->building;
        if ($building) {
            $buildingVal = $building->getSetting($key);
            if ($buildingVal !== null) {
                return $buildingVal;
            }
        }
        return $default;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEndingSoon($query, int $days = 30)
    {
        return $query->where('status', 'active')
            ->where('end_date', '<=', now()->addDays($days))
            ->where('end_date', '>=', now());
    }
}
