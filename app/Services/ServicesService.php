<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ServicesService extends BaseCrudService
{
    protected function get_model(): Model
    {
        return Service::getModel();
    }
}
