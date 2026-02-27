<?php

namespace App\Services;

use App\Models\LocationAddress;
use App\Models\MobileUser;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MobileUserService
{
    public function getUserOrders(?string $status = null, MobileUser $user): Collection
    {
        $query = Order::where('mobile_user_id', $user->id);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->latest()->get();
    }

    public function getUserOrderById(int $id, $user): ?Order
    {
        return Order::find($id);
    }

    public function updateUserProfile(array $data): MobileUser
    {
        $user = MobileUser::findOrFail(Auth::id());
        $user->update($data);

        return $user->fresh();
    }



    public function createOrUpdateAddress(array $data, MobileUser $user): LocationAddress
    {
        $data['mobile_user_id'] = $user->id;

        return LocationAddress::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );
    }

    public function deleteAddress(LocationAddress $address): bool
    {


        return $address->delete();
    }
}
