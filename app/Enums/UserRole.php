<?php

namespace App\Enums;



enum UserRole: int
{
    case ADMIN = 1;
    case COMPANY = 2;
    case DEBUG_ADMIN = 3;
}
