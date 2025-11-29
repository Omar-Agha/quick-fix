<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationAddress extends Model
{
    protected $fillable = ['mobile_user_id', 'address_type', 'address', 'full_address'];
    public $timestamps = false;
    public function mobileUser(): BelongsTo
    {
        return $this->belongsTo(MobileUser::class);
    }
    public $casts = [
        'address_type' => AddressType::class,
    ];
}
