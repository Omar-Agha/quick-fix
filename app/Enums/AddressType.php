<?php

namespace App\Enums;


enum AddressType: int
{
    case HOME = 1;
    case WORK = 2;
    case OTHER = 3;
}
