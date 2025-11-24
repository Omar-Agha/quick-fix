<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;

class ServicesApiController extends Controller
{
    public function __construct(
        private ServiceService $serviceService
    ) {}

    public function getAllServices()
    {
        $services = $this->serviceService->getAllServices();

        return response()->json($services);
    }
}
