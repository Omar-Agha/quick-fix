<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MobileAppService;

class MobileAppApiController extends Controller
{
    public function __construct(
        private MobileAppService $mobileAppService
    ) {}

    public function getAllServices()
    {
        $services = $this->mobileAppService->getAllServices();

        return response()->json($services);
    }

    public function getAllBannerAds()
    {
        $forHomePage = request()->has('for_home_page') ? true : null;
        $bannerAds = $this->mobileAppService->getAllBannerAds($forHomePage);

        return response()->json($bannerAds);
    }

    public function getAllOffers()
    {
        $offers = $this->mobileAppService->getAllOffers();

        return response()->json($offers);
    }

    public function getAllArticles()
    {
        $articles = $this->mobileAppService->getAllArticles();

        return response()->json($articles);
    }
}
