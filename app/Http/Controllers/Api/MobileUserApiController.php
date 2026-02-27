<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\OrderDto;
use App\Models\ChangePhoneNumberRequest;
use App\Models\LocationAddress;
use App\Models\MobileUser;
use App\Models\Order;
use App\Services\AuthService;
use App\Services\MobileUserService;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Propaganistas\LaravelPhone\Rules\Phone;

class MobileUserApiController extends Controller
{
    public function __construct(
        private MobileUserService $mobileUserService,
        private AuthService $authService
    ) {}

    public function getUser()
    {
        $user = request()->user('customer');
        $user->load('locationAddresses');
        return $this->responseSuccess($user, 'User fetched successfully');
    }



    public function getUserOrders()
    {
        $validated = request()->validate([
            // 'status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
        ]);

        // $orders = $this->mobileUserService->getUserOrders($validated['status'] ?? null, request()->user('customer'));
        $orders = Order::whereBelongsTo(request()->user('customer'))
            ->when($validated['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->with(['files'])
            ->latest()
            ->paginate(request()->get('per_page', 10), page: request()->get('page', 1));
        return new PaginatedResourceResponse(OrderDto::collection($orders));

        // return $this->responseSuccess(OrderDto::collection($orders), 'Orders fetched successfully');
        // return $this->responseSuccess($paginatedResponse, 'Orders fetched successfully');
    }
    public function getUserOrderById($id)
    {
        $user = request()->user('customer');
        // $order = $this->mobileUserService->getUserOrderById($id, $user);
        $order = Order::find($id);




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
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'home_phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:mobile_users,email,' . auth('customer')->user()->id . ',id',
            'delete_avatar' => 'boolean|nullable'
        ]);
        if (request('delete_avatar')) {
            $validated['avatar'] = null;
        }
        if (request()->hasFile('avatar')) {
            $validated['avatar'] = request()->file('avatar')->store('avatars', 'public');
        }

        $user = request()->user('customer');
        $user->update($validated);

        return $this->responseSuccess($user, 'User profile updated successfully');
    }

    public function deleteUserAccount()
    {
        request()->validate(['password' => 'required|string']);
        if (!Hash::check(request()->input('password'), request()->user('customer')->password)) {
            return response()->json([
                'message' => 'Invalid password',
            ], 401);
        }
        request()->user('customer')->delete();
        return $this->responseSuccess(null, 'User account deleted successfully');
    }
    public function changePassword()
    {
        $validated = request()->validate([
            'old_password' => 'required|string',
            'new_password' => ['required', 'string', 'min:8', 'confirmed', Password::default()],
        ]);
        $user = request()->user('customer');
        if (!Hash::check(request()->input('old_password'), $user->password)) {

            return response()->json([
                'message' => 'Invalid old password',
            ], 401);
        }
        $user->update([
            'password' => Hash::make(request()->input('new_password')),
        ]);
        return $this->responseSuccess(null, 'Password changed successfully');
    }

    public function requestChangePhoneNumber()
    {
        /**
         * @var MobileUser $user
         */
        $user = request()->user('customer');
        $user->load('currentChangePhoneNumberRequest');
        // return $user;
        $validated = request()->validate([
            'new_phone_number' => [
                'required',
                'string',
                Rule::unique('mobile_users', 'phone_number')->ignore(request()->user('customer')->id),
                (new Phone)->international()->country([config('app.supported_countries')])
            ],
        ]);
        $currentChangePhoneNumberRequest = $user->currentChangePhoneNumberRequest;

        if ($currentChangePhoneNumberRequest) {
            return $this->responseError(['message' => 'You already have a pending change phone number request'], 400);
        }
        if ($user->phone_number == $validated['new_phone_number']) {
            return $this->responseError(['message' => 'New phone number is the same as the current phone number'], 400);
        }

        $otp = $this->authService->sendOtpForUser($user);
        $changePhoneNumberRequest = ChangePhoneNumberRequest::create([
            'mobile_user_id' => $user->id,
            'new_phone_number' => $validated['new_phone_number'],
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // $user->sendOtpForChangePassword();
        return $this->responseSuccess(null, 'OTP sent successfully');
    }

    public function verifyOtpForChangePhoneNumber()
    {
        $validated = request()->validate([
            'otp' => 'required|string',
        ]);
        /**
         * @var MobileUser $user
         */

        $user = request()->user('customer');
        $changePhoneNumberRequest = $user->currentChangePhoneNumberRequest;


        if (!$changePhoneNumberRequest) {
            return $this->responseError(['message' => 'No pending change phone number request found'], 400);
        }

        if ($changePhoneNumberRequest->otp != $validated['otp']) {
            return $this->responseError(['message' => 'Invalid OTP'], 400);
        }
        $user->update([
            'phone_number' => $changePhoneNumberRequest->new_phone_number,
        ]);
        $changePhoneNumberRequest->update([
            'verified_at' => now(),
            'expired_at' => now(),
            'verified' => true,
        ]);
        return $this->responseSuccess(null, 'Phone number changed successfully');
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


        return $this->responseSuccess($user->locationAddresses, 'Addresses fetched successfully');
    }
}
