<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_percentage',
    ];

    public $timestamps = true;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function mobileUser(): HasManyThrough
    {
        return $this->hasManyThrough(MobileUser::class, Order::class);
    }
}
