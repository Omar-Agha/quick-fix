<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangePhoneNumberRequest extends Model
{
    protected $fillable = [
        'mobile_user_id',
        'new_phone_number',
        'otp',
        'otp_expires_at',
        'verified',
        'verified_at',
        'expired_at',
    ];
    public $timestamps = true;
    protected $casts = [
        'verified' => 'boolean',
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at < now();
    }
    public function mobileUser(): BelongsTo
    {
        return $this->belongsTo(MobileUser::class);
    }
}
