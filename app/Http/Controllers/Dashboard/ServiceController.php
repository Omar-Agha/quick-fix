<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\BaseCrudService;
use App\Services\ServicesService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
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
    protected function get_model(): Builder
    {
        return Service::query();
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
            'cost_per_worker' => 'required|numeric',
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',

        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_per_worker' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
        ];
    }
    protected function setImages($data)
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $image_path = $image->store('images', 'public');
            if (!$image_path) {
                throw new Exception("Server error: Failed to upload image");
            }
            $data['image'] = $image_path;
        }
        return $data;
    }
}
