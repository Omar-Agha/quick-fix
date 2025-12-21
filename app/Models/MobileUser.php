<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class MobileUser extends Model
{
    use HasApiTokens, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
        'password',
        'otp_code',
        'otp_verified',
        'full_name',
        'avatar',
        'home_phone',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp_code',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'otp_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function locationAddresses(): HasMany
    {
        return $this->hasMany(LocationAddress::class)->where('is_deleted', false);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, Order::class);
    }
    public function getAvatarAttribute($value)
    {
        if (!$value) return null;
        return asset('storage/' . $value);
    }
    public function changePhoneNumberRequests(): HasMany
    {
        return $this->hasMany(ChangePhoneNumberRequest::class);
    }
    public function currentChangePhoneNumberRequest(): HasOne|null
    {
        return $this->changePhoneNumberRequests()
            ->one()
            ->whereNowOrFuture('otp_expires_at')
            ->where('verified', false);
    }
}
