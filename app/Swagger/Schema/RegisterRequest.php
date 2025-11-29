<?php

namespace App\Swagger\Schema;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     title="Register Request",
 *     description="Request data for registering a new user",
 *     required={"name", "email", "password", "password_confirmation"},
 * )
 */
class RegisterRequest
{
    /**
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     * )
     */
    public $phone_number;

    /**
     * @OA\Property(
     *     property="password",
     *     type="string"
     * )
     */
    public $password;
}


/**
 * @OA\Schema(
 *     schema="VerifyOtpRequest",
 *     type="object",
 *     title="Verify OTP Request",
 *     description="Request data for verifying OTP",
 *     required={"phone_number", "otp_code"},
 * )
 */


 //todo: add oa swagger specification
/**
 * @OA\Schema(
 *     schema="VerifyOtpRequest",
 *     type="object",
 *     title="Verify OTP Request",
 *     description="Request data for verifying OTP",
 *     required={"phone_number", "otp_code"},
 * )
 */
class   VerifyOtpRequest
{
    /**
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     * )
     */
    public $phone_number;


    /**
     * @OA\Property(
     *     property="otp_code",
     *     type="string"
     * )
     */
    public $otp_code;
}

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     title="Login Request",
 *     description="Request data for logging in a user",
 *     required={"phone_number", "password"},
 * )
 */
class LoginRequest
{

    /**
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     * )
     */
    public $phone_number;

    /**
     * @OA\Property(
     *     property="password",
     *     type="string"
     * )
     */
    public $password;
}

/**
 * @OA\Schema(
 *     schema="Service",
 *     type="object",
 *     title="Service",
 *     description="Service data",
 *     required={"name", "description", "price", "image"},
 * )
 */
class Service
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string"
     * )
     */
    public $description;

    /**
     * @OA\Property(
     *     property="price",
     *     type="number"
     * )
     */
    public $price;

    /**
     * @OA\Property(
     *     property="image",
     *     type="string"
     * )
     */
    public $image;
}

/**
 * @OA\Schema(
 *     schema="ResendOtpRequest",
 *     type="object",
 *     title="Resend OTP Request",
 *     description="Request data for resending OTP",
 *     required={"phone_number"},
 * )
 */
class ResendOtpRequest
{
    /**
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     * )
     */
    public $phone_number;
}
