<?php

namespace App\Http\Controllers\Api;

use App\Contracts\OrderItemRequestDto;
use App\Core\FeeCalculator;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetOrderRequest;
use App\Http\Resources\Dashboard\OrderDto;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Service;
use App\Services\OrderService;
use Illuminate\Validation\Rule;

class OrderApiController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private FeeCalculator $feeCalculator
    ) {}

    /**
     * @OA\Post(
     *     path="/api/set-order",
     *     summary="Set an order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="service_id", type="integer"), @OA\Property(property="reserve_datetime", type="string", format="date-time"), @OA\Property(property="address_id", type="integer"), @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary")), @OA\Property(property="payment_method", type="string")),
     *     ),
     *     @OA\Response(response=200, description="Order set successfully"),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Service not found"),
     *     @OA\Response(response=409, description="Order already exists"),
     * )
     */
    public function setOrder(SetOrderRequest $request)
    {
        $validated = $request->validated();

        $items = OrderItemRequestDto::collect($validated['items']);

        try {
            $order = $this->orderService->createOrder(

                request()->user('customer'),
                collect($items),
                request()->file('images') ?? []
            );
            return $this->responseSuccess($order, 'yes');
            // return response()->json(new OrderDto($order));
        } catch (\Exception $e) {
            // return $this->responseError($e->getMessage());
            return $this->responseError([
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/cancel-order",
     *     summary="Cancel an order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="order_id", type="integer")),
     *     ),
     *     @OA\Response(response=200, description="Order cancelled successfully"),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Order not found"),
     * )
     */
    public function cancelOrder(Order $order)
    {

        try {
            $cancelled = $this->orderService->cancelOrder($order, request()->user('customer'));
        } catch (\Exception $e) {
            return $this->responseError([
                'message' => $e->getMessage(),
            ]);
        }

        return $this->responseSuccess(new OrderDto($cancelled), 'Order cancelled successfully');
    }

//calculate fee api is GET request
    /**
     * @OA\Post(
     *     path="/api/calculate-service-fees",
     *     summary="Calculate service fees",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="service_id", type="integer")),
     *     ),
     *     @OA\Response(response=200, description="Service fees calculated successfully"),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Service not found"),
     * )
     */
    public function calculateServiceFees()
    {
        $validated = request()->validate([

            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.number_of_workers' => 'required|integer|max:5|min:1',
            'is_direct_service' => 'required|boolean',
            'reserve_datetime' => [Rule::requiredIf(request('is_direct_service') == false), Rule::date()->after(now())],
            'payment_method' => 'required|in:cash,card',
            'coupon' => 'nullable|string|max:255',
        ]);

        $items = OrderItemRequestDto::collect($validated['items']);

        return response()->json([
            'message' => 'Service fees calculated successfully',
            'order_summery' => $this->feeCalculator->calculateServiceFees(collect($items), request('coupon'), request()->user('customer')),

        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/verify-coupon",
     *     summary="Verify a coupon",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="coupon", type="string")),
     *     ),
     *     @OA\Response(response=200, description="Coupon verified successfully"),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Coupon not found"),
     * )
     */
    public function verifyCoupon(string $coupon)
    {
        try {
            $this->feeCalculator->verifyCoupon($coupon, request()->user('customer'));
        } catch (\Exception $e) {
            return $this->responseError(['coupon' => $e->getMessage()], $e->getMessage());
        }
        return $this->responseSuccess(['coupon' => 'valid'], 'coupon is valid');
    }
}
