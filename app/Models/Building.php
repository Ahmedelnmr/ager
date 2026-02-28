<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'address', 'city', 'notes', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function activeUnits(): HasMany
    {
        return $this->hasMany(Unit::class)->where('status', 'rented');
    }

    public function vacantUnits(): HasMany
    {
        return $this->hasMany(Unit::class)->where('status', 'vacant');
    }

    /**
     * Get a specific setting value, falling back to a default.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
