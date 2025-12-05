<?php

namespace App\Services\Dashboard;

use App\Models\BannerAd;
use App\Models\Order;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderService extends BaseCrudService
{
    protected function get_model(): Builder
    {


        return Order::with(['orderItems', 'orderItems.service', 'locationAddress']);
    }
}
