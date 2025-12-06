<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\BannerAdService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerAdCrudController extends BaseCrudController
{
    public function __construct(private BannerAdService $bannerAdService)
    {
        return parent::__construct($this->bannerAdService);
    }
    protected function index_view()
    {
        return Inertia::render('BannerAds/Index');
    }
    protected function storeRequestRules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
