<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Model;

class LocationAddress extends Model
{
    protected $fillable = ['mobile_user_id', 'address_type', 'address', 'full_address'];

    public $casts = [
        'address_type' => AddressType::class,
    ];
}
