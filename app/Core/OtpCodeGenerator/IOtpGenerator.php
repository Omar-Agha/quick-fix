<?php

namespace App\Core\OtpCodeGenerator;

interface IOtpGenerator
{
    /**
     * Generate a One Time Password (OTP) code.
     *
     * @return string
     */
    public function generate(): string;
}
