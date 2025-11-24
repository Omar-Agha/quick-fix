<?php

namespace App\Services;

use App\Models\LocationAddress;
use App\Models\MobileUser;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MobileUserService
{
    public function getUserOrders(?string $status = null): Collection
    {
        $query = Order::where('mobile_user_id', Auth::id());

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->latest()->get();
    }

    public function getUserOrderById(int $id): ?Order
    {
        return Order::where('mobile_user_id', Auth::id())->find($id);
    }

    public function updateUserProfile(array $data): MobileUser
    {
        $user = MobileUser::findOrFail(Auth::id());
        $user->update($data);

        return $user->fresh();
    }

    public function deleteUserAccount(): bool
    {
        $user = MobileUser::findOrFail(Auth::id());

        return $user->delete();
    }

    public function createOrUpdateAddress(array $data): LocationAddress
    {
        $data['mobile_user_id'] = Auth::id();

        return LocationAddress::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );
    }

    public function deleteAddress(LocationAddress $address): bool
    {
        if ($address->mobile_user_id !== Auth::id()) {
            return false;
        }

        return $address->delete();
    }
}
