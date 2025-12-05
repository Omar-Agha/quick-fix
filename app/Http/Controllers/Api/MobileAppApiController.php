<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceDto;
use App\Models\BannerAd;
use App\Models\MobileUser;
use App\Models\Offer;
use App\Models\Service;
use App\Services\MobileAppService;

class MobileAppApiController extends Controller
{
    public function __construct(
        private MobileAppService $mobileAppService
    ) {}



    /**
     * @OA\Get(
     *     path="/api/all-services",
     *     summary="Get all services",
     *     tags={"Mobile App"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="App\Swagger\Schema\Service")
     *         )
     *     )
     * )
     */
    public function getAllActiveServices()
    {
        $services = Service::whereIsActive(true)->get();

        return response()->json(ServiceDto::collection($services));
    }

    /**
     * @OA\Get(
     *     path="/api/all-banner-ads",
     *     summary="Get all banner ads",
     *     tags={"Mobile App"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="App\Swagger\Schema\BannerAd"))
     *     )
     * )
     */
    public function getAllActiveBannerAds()
    {
        $forHomePage = request()->has('for_home_page') ? true : null;
        $bannerAds = BannerAd::whereIsActive(true)->get();

        return response()->json($bannerAds);
    }

    /**
     * @OA\Get(
     *     path="/api/all-offers",
     *     summary="Get all offers",
     *     tags={"Mobile App"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *         )
     *     )
     * )
     */
    public function getAllActiveOffers()
    {
        $offers = Offer::whereIsActive(true)->get();

        return response()->json($offers);
    }

    public function getAllArticles()
    {
        $articles = $this->mobileAppService->getAllArticles();

        return response()->json($articles);
    }
}
