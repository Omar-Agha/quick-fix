<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MobileAppService;

class MobileAppApiController extends Controller
{
    public function __construct(
        private MobileAppService $mobileAppService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/all-services",
     *     tags={"Services"},
     *     summary="Get all services",
     *     description="Return list of services",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function getAllServices()
    {
        $services = $this->mobileAppService->getAllServices();

        return response()->json($services);
    }

    /**
     * @OA\Get(
     *     path="/api/all-banner-ads",
     *     tags={"Banner Ads"},
     *     summary="Get all banner ads",
     *     description="Return list of banner ads",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function getAllBannerAds()
    {
        $forHomePage = request()->has('for_home_page') ? true : null;
        $bannerAds = $this->mobileAppService->getAllBannerAds($forHomePage);

        return response()->json($bannerAds);
    }

    /**
     * @OA\Get(
     *     path="/api/all-offers",
     *     tags={"Offers"},
     *     summary="Get all offers",
     *     description="Return list of offers",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function getAllOffers()
    {
        $offers = $this->mobileAppService->getAllOffers();

        return response()->json($offers);
    }

    /**
     * @OA\Get(
     *     path="/api/all-articles",
     *     tags={"Articles"},
     *     summary="Get all articles",
     *     description="Return list of articles",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function getAllArticles()
    {
        $articles = $this->mobileAppService->getAllArticles();

        return response()->json($articles);
    }
}
