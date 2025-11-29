<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Http\Controllers\Controller;
use App\Models\LocationAddress;
use App\Services\MobileUserService;
use Illuminate\Validation\Rule;

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
            'email' => 'email|unique:mobile_users,email,' . auth()->user()->id . ',id',
            'mobile_number' => 'string|max:255|unique:mobile_users,mobile_number,' . auth()->user()->id . ',id',
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





    /**
     * @OA\Post(
     *     path="/api/user-address",
     *     summary="Create or update user address",
     *     tags={"Mobile App"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="App\Swagger\Schema\LocationAddress")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Address created or updated successfully")
     *         )
     *     )
     * )
     */
    public function createOrUpdateAddress()
    {
        $validated = request()->validate([
            'id' => 'nullable|exists:location_addresses,id',
            'address_type' => [Rule::enum(AddressType::class), 'required'],
            'address' => 'string|max:255',
            'full_address' => 'string|max:255',
        ]);

        $address = $this->mobileUserService->createOrUpdateAddress($validated, request()->user('customer'));

        return response()->json([
            'message' => 'Address created or updated successfully',
            'address' => $address,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-address/{id}",
     *     summary="Delete user address",
     *     tags={"Mobile App"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         
     *     )
     * )
     */
    public function deleteAddress(LocationAddress $address)
    {
        if ($address->mobile_user_id !== request()->user('customer')->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->mobileUserService->deleteAddress($address);

        return response()->json([
            'message' => 'Address deleted successfully',
            'address' => $address,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user-address",
     *     summary="Get user addresses",
     *     tags={"Mobile App"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="App\Swagger\Schema\LocationAddress")
     *         )
     *     )
     * )
     */
    public function getUserAddresses()
    {
        $user = request()->user('customer');


        return response()->json($user->locationAddresses);
    }
}
