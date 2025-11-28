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
