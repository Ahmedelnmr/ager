<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id', 'contract_id', 'reported_by', 'description',
        'status', 'assigned_to', 'cost', 'started_at', 'finished_at', 'attachments',
    ];

    protected $casts = [
        'cost'        => 'decimal:2',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
