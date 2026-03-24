<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Service $service): array => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description ?? '',
                'image' => $service->image,
            ]);

        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }
}
