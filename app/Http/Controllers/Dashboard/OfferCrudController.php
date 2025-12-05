<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\BannerAdService;
use App\Services\Dashboard\OfferService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfferCrudController extends BaseCrudController
{
    public function __construct(private OfferService $offerService)
    {
        return parent::__construct($this->offerService);
    }
    protected function index_view()
    {
        return Inertia::render('Offers/Index');
    }
    protected function storeRequestRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'url',
            'is_active' => 'boolean',
        ];
    }
    protected function setImages($data)
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $image_path = $image->store('images', 'public');
            $data['image'] = $image_path;
        }
        return $data;
    }
}
