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

/**
 * @OA\Schema(
 *     schema="LocationAddress",
 *     type="object",
 *     title="Location Address",
 *     description="Location address data",
 *     required={"id", "address_type", "address", "full_address"},
 * )
 */
class LocationAddress
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer"
     * )
     */
    public $id;

    /**
     * @OA\Property(
     *     property="address_type",
     *     type="string"
     * )
     */
    public $address_type;

    /**
     * @OA\Property(
     *     property="address",
     *     type="string"
     * )
     */
    public $address;


    /**
     * @OA\Property(
     *     property="full_address",
     *     type="string"
     * )
     */
    public $full_address;
}

/**
 * @OA\Schema(
 *     schema="CreateOrUpdateAddressRequest",
 *     type="object",
 *     title="Create or update address request",
 *     description="Request data for creating or updating an address",
 *     required={"id", "address_type", "address", "full_address"},
 * )
 */
class CreateOrUpdateAddressRequest
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     nullable=true
     * )
     */
    public ?int $id = null;
    /**
     * @OA\Property(
     *     property="address_type",
     *     type="string",
     *     @OA\Schema(enum={"A","B","C"})
     * )
     * */
    public $address_type;

    /**
     * @OA\Property(
     *     property="address",
     *     type="string"
     * )
     */
    public $address;

    /**
     * @OA\Property(
     *     property="full_address",
     *     type="string"
     * )
     */
    public $full_address;
}
