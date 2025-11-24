<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationAddress;
use App\Services\MobileUserService;

class MobileUserApiController extends Controller
{
    public function __construct(
        private MobileUserService $mobileUserService
    ) {}

    public function getUserOrders()
    {
        $validated = request()->validate([
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
        ]);

        $orders = $this->mobileUserService->getUserOrders($validated['status'] ?? null);

        return response()->json($orders);
    }

    public function getUserOrderById($id)
    {
        $order = $this->mobileUserService->getUserOrderById($id);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json($order);
    }

    public function updateUserProfile()
    {
        $validated = request()->validate([
            'full_name' => 'string|max:255',
            'email' => 'email|unique:mobile_users,email,'.auth()->user()->id.',id',
            'mobile_number' => 'string|max:255|unique:mobile_users,mobile_number,'.auth()->user()->id.',id',
            'address' => 'string|max:255',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
        ]);

        $user = $this->mobileUserService->updateUserProfile($validated);

        return response()->json($user);
    }

    public function deleteUserAccount()
    {
        $this->mobileUserService->deleteUserAccount();

        return response()->json([
            'message' => 'User account deleted successfully',
        ]);
    }

    public function createOrUpdateAddress()
    {
        $validated = request()->validate([
            'id' => 'nullable|exists:location_addresses,id',
            'address_type' => 'in:home,work,other',
            'address' => 'string|max:255',
            'full_address' => 'string|max:255',
        ]);

        $this->mobileUserService->createOrUpdateAddress($validated);

        return response()->json([
            'message' => 'Address created or updated successfully',
        ]);
    }

    public function deleteAddress(LocationAddress $address)
    {
        $deleted = $this->mobileUserService->deleteAddress($address);

        if (! $deleted) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'message' => 'Address deleted successfully',
        ]);
    }
}
