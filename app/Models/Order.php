<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{

    use HasFiles;

    protected $fillable = [
        'mobile_user_id',
        'location_address_id',
        'is_direct_service',
        'total_cost',
        'fees',
        'pay_at_cashier',
        'payment_method',
        'status',
        'reserve_datetime',
        'description',
        'coupon_id',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'reserve_datetime' => 'datetime',
    ];

    public $timestamps = true;

    public function mobileUser(): BelongsTo
    {
        return $this->belongsTo(MobileUser::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function locationAddress(): BelongsTo
    {
        return $this->belongsTo(LocationAddress::class, 'location_address_id');
    }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
