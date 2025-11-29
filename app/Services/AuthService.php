<?php

namespace App\Services;

use App\Events\SendOtpToUser;
use App\Models\MobileUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new mobile user and generate OTP.
     */
    public function register(string $phoneNumber, string $password): MobileUser
    {
        $user = MobileUser::create([
            'phone_number' => $phoneNumber,
            'password' => $password,
            'otp_verified' => false,
        ]);

        $otp = random_int(100000, 999999);
        $user->otp_code = $otp;
        $user->save();

        // Here you should send the OTP to the user's phone (e.g., with SMS)
        event(new SendOtpToUser($user, $otp));

        return $user;
    }

    /**
     * Verify OTP for a mobile user.
     *
     * @return array{user: MobileUser, was_verified: bool}
     */
    public function verifyOtp(string $phoneNumber, string $otpCode): array
    {
        $user = MobileUser::where('phone_number', $phoneNumber)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone_number' => ['No user found with this phone number.'],
            ]);
        }

        $wasAlreadyVerified = $user->otp_verified;

        if ($wasAlreadyVerified) {
            return [
                'user' => $user,
                'was_verified' => true,
            ];
        }

        if ($user->otp_code !== $otpCode) {
            throw ValidationException::withMessages([
                'otp_code' => ['OTP code is incorrect.'],
            ]);
        }

        $user->otp_verified = true;
        $user->otp_code = null;
        $user->save();

        return [
            'user' => $user,
            'was_verified' => false,
        ];
    }

    /**
     * Authenticate a mobile user and return access token.
     */
    public function login(string $phoneNumber, string $password): array
    {
        $user = MobileUser::where('phone_number', $phoneNumber)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone_number' => ['No user found with this phone number.'],
            ]);
        }

        if (! $user->otp_verified) {
            $exception = ValidationException::withMessages([
                'phone_number' => ['Phone number not verified.'],
            ]);
            $exception->status = 403;
            throw $exception;
        }

        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password is incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout the authenticated user (current device only).
     */
    public function logout(MobileUser $user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $user->tokens()->where('id', $token->id)->delete();
        }
    }

    /**
     * Logout from all other devices (keeps the current token active).
     */
    public function logoutFromOtherDevices(MobileUser $user): void
    {
        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            $user->tokens()
                ->where('id', '!=', $currentToken->id)
                ->delete();
        } else {
            // If no current token, delete all tokens
            $user->tokens()->delete();
        }
    }

    public function resendOtp(string $phoneNumber): void
    {
        $user = MobileUser::where('phone_number', $phoneNumber)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone_number' => ['No user found with this phone number.'],
            ]);
        }
        $otp = random_int(100000, 999999);
        $user->otp_code = $otp;
        $user->save();

        // Here you should send the OTP to the user's phone (e.g., with SMS)
        event(new SendOtpToUser($user, $otp));
    }
}
