<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseCrudController;
use App\Services\Dashboard\CompanyService;
use Inertia\Inertia;

class CompanyCrudController extends BaseCrudController
{
    public function __construct(private CompanyService $companyService)
    {
        return parent::__construct($this->companyService);
    }
    protected function index_view()
    {
        return Inertia::render('Companies/Index');
    }
    protected function storeRequestRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|file|image|max:2048',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8',
        ];
    }
    protected function updateRequestRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|file|image|max:2048',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8',
        ];
    }
    protected function setImages($data)
    {
        if (request()->hasFile('logo')) {
            $logo = request()->file('logo');
            $logo_path = $logo->store('logos', 'public');
            $data['logo'] = $logo_path;
        }
        return $data;
    }
}
