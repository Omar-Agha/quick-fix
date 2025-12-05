<?php

namespace App\Services\Dashboard;

use App\Models\Offer;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OfferService extends BaseCrudService
{
    protected function get_model(): Builder
    {
        return Offer::query();
    }
}
