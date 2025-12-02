<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{

    use HasFiles;

    protected $fillable = [
        'mobile_user_id',
        'service_id',
        'location_address_id',
        'payment_method',
        'status',
        'cost',
        'fees',
        'reserve_datetime',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public $timestamps = true;

    public function mobileUser(): BelongsTo
    {
        return $this->belongsTo(MobileUser::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function locationAddress(): BelongsTo
    {
        return $this->belongsTo(LocationAddress::class, 'location_address_id');
    }
}
