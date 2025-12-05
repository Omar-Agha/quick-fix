<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\BannerAdService;
use App\Services\Dashboard\CouponService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponCrudController extends BaseCrudController
{
    public function __construct(private CouponService $couponService)
    {
        return parent::__construct($this->couponService);
    }
    protected function index_view()
    {
        return Inertia::render('Coupons/Index');
    }
    protected function storeRequestRules(): array
    {
        return [
            'code' => 'required|string|size:4|unique:coupons,code',
            'discount_percentage' => 'required|integer|min:1|max:100',
        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'code' => 'required|string|size:4|unique:coupons,code',
            'discount_percentage' => 'required|integer|min:1|max:100',
        ];
    }
    protected function setImages($data)
    {

        return $data;
    }
}
