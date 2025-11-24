<?php

namespace App\Services;

use App\Models\Article;
use App\Models\BannerAd;
use App\Models\Offer;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class MobileAppService
{
    public function getAllServices(): Collection
    {
        return Service::all();
    }

    public function getAllBannerAds(?bool $forHomePage = null): Collection
    {
        $query = BannerAd::query();
        $query->when($forHomePage, function ($query) {
            return $query->where('for_home_page', true);
        });


        return $query->get();
    }

    public function getAllOffers(): Collection
    {
        return Offer::all();
    }

    public function getAllArticles(): Collection
    {
        return Article::all();
    }
}
