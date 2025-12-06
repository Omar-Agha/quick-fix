<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\OrderDto;
use App\Services\Dashboard\BannerAdService;
use App\Services\Dashboard\OrderService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderCrudController extends BaseCrudController
{
    protected function resource($data)
    {
        return new OrderDto($data);
    }
    public function __construct(private OrderService $order_service)
    {
        return parent::__construct($this->order_service);
    }
    protected function index_view()
    {
        return Inertia::render('Orders/Index');
    }
    protected function storeRequestRules(): array
    {
        return [];
    }
    protected function updateRequestRules(): array
    {
        return [];
    }
}
