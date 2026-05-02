<?php

namespace App\Core\OtpCodeGenerator;

class FixedOtpGenerator implements IOtpGenerator
{
    /**
     * Generate a One Time Password (OTP) code.
     *
     * @return string
     */
    public function generate(): string
    {
        return '9999';
    }
}
