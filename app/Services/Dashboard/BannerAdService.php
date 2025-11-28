<?php

namespace App\Services\Dashboard;

use App\Models\BannerAd;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Model;

class BannerAdService extends BaseCrudService
{
    protected function get_model(): Model
    {
        return BannerAd::getModel();
    }
}
