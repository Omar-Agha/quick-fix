<?php

namespace App\Enums;



/**
 * @OA\Schema(
 *     schema="AddressType",
 *     type="string",
 *     enum={App\Enums\AddressType::class}
 * )
 */
enum AddressType: int
{
    case HOME = 1;
    case WORK = 2;
    case OTHER = 3;
}
