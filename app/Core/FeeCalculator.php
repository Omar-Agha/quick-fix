<?php

namespace App\Core;

use App\Models\MobileUser;
use App\Models\Service;

class FeeCalculator
{
    public function calculateServiceFees(Service $service, MobileUser $mobileUser)
    {
        $fees = 0;
        // if ($mobileUser->is_vip) {
        //     $fees = $service->price * 0.1;
        // }
        return $fees;
    }
}
