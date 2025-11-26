<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\BaseCrudService;
use App\Services\ServicesService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class ServiceController extends BaseCrudController
{

    public function __construct(private ServicesService $service)
    {
        return parent::__construct($this->service);
    }
    protected function get_model(): Model
    {
        return Service::model();
    }
    protected function index_view(): Response
    {
        return Inertia::render('Services/Index');
    }
    protected function storeRequestRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
