<?php


namespace App\Traits;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasCoupons
{
    public function coupons(): MorphMany
    {
        return $this->morphMany(Coupon::class, 'couponable');
    }
    public function storeCoupon(Coupon $coupon): void
    {
        $this->coupons()->save($coupon);
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        $coupon->delete();
    }
}
