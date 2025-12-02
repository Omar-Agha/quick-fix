<?php

namespace App\Http\Controllers\Api;

use App\Core\FeeCalculator;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDto;
use App\Models\Order;
use App\Models\Service;
use App\Services\OrderService;

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
    public function setOrder()
    {
        $validated = request()->validate([
            'service_id' => 'required|exists:services,id',
            'reserve_datetime' => 'required|after:now|date_format:Y-m-d H:i:s',
            'address_id' => 'required|exists:location_addresses,id',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_method' => 'required|in:cash,card',
        ]);



        try {
            $order = $this->orderService->createOrder(
                $validated,
                request()->user('customer'),
                Service::find($validated['service_id']),
                request()->file('images') ?? []
            );

            return response()->json(new OrderDto($order));
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
     * @OA\Get(
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
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::find($validated['service_id']);

        return response()->json([
            'message' => 'Service fees calculated successfully',
            'fees' => $this->feeCalculator->calculateServiceFees($service, request()->user('customer')),
            'cost' => $service->price,
        ]);
    }
}
