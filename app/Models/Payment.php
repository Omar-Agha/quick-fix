<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_reference',
        'gateway_transaction_id',
        'status',
        'amount',
        'metadata',
        'initiated_at',
        'completed_at',
        'redirect_url',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'metadata' => 'array',
            'initiated_at' => 'datetime',
            'completed_at' => 'datetime',
            'redirect_url' => 'string',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [
            PaymentStatus::COMPLETED,
            PaymentStatus::FAILED,
            PaymentStatus::EXPIRED,
            PaymentStatus::CANCELLED,
        ], true);
    }
}
