<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\ArticleService;
use App\Services\Dashboard\BannerAdService;
use App\Services\Dashboard\OfferService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleCrudController extends BaseCrudController
{
    public function __construct(private ArticleService $articleService)
    {
        return parent::__construct($this->articleService);
    }
    protected function index_view()
    {
        return Inertia::render('Articles/Index');
    }
    protected function storeRequestRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
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
