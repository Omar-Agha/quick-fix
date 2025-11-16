<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        public AuthService $authService
    ) {}

    /**
     * Register a new mobile user and issue an OTP for verification.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authService->register(
            $request->validated()['phone_number'],
            $request->validated()['password']
        );

        return response()->json([
            'message' => 'User registered. Please verify OTP sent to your phone.',
        ]);
    }

    /**
     * Verify OTP for registration.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->validated()['phone_number'],
            $request->validated()['otp_code']
        );

        if ($result['was_verified']) {
            return response()->json([
                'message' => 'User already verified.',
            ], 200);
        }

        return response()->json([
            'message' => 'Phone number successfully verified.',
        ], 200);
    }

    /**
     * Login endpoint - Only allows verified users to login via phone_number & password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $tokenData = $this->authService->login(
            $request->validated()['phone_number'],
            $request->validated()['password']
        );

        return response()->json($tokenData);
    }

    /**
     * Logout endpoint - Revokes the user's current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $this->authService->logout($user);
        }

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }

    /**
     * Logout from other devices endpoint - Revokes all tokens except the current one.
     */
    public function logoutFromOtherDevices(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $this->authService->logoutFromOtherDevices($user);
        }

        return response()->json([
            'message' => 'Successfully logged out from other devices.',
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
