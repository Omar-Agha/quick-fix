<?php

namespace App\Core\OtpCodeGenerator;

class RandomOtpGenerator implements IOtpGenerator
{
    /**
     * Generate a secure One Time Password (OTP) code.
     *
     * @return string
     */
    public function generate(): string
    {
        return (string) random_int(1000, 9999);
    }
}
