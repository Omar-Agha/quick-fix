<?php

namespace App\Services\Dashboard;

use App\Models\Coupon;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CouponService extends BaseCrudService
{
    protected function get_model(): Builder
    {
        return Coupon::with('orders')->withCount(['orders']);
    }
}
