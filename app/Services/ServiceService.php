<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function getAllServices(): Collection
    {
        return Service::all();
    }
}
