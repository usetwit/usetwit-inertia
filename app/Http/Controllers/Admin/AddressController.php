<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Addresses\CreateUserRequest;
use App\Http\Requests\Admin\Addresses\UpdateUserRequest;
use App\Models\Address;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function userCreate(User $user, CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (! $user->addresses()->exists()) {
            $data['is_default'] = true;
        }

        $user->addresses()->create($data);

        $user->load('addresses');

        return response()->json([
            'addresses' => $user->addresses,
            'message' => 'Address added',
        ], 201);
    }

    private function makeDefault(User|Company|Location $model, Address $address): JsonResponse
    {
        $address->update(['is_default' => true]);

        return response()->json([
            'addresses' => $model->addresses,
            'message' => 'Address set as default',
        ]);
    }

    public function userMakeDefault(User $user, Address $address): JsonResponse
    {
        return $this->makeDefault($user, $address);
    }

    public function userDestroy(User $user, Address $address): JsonResponse
    {
        $wasDefault = $address->is_default;

        $address->delete();

        if ($wasDefault) {
            $newDefault = $user
                ->addresses()
                ->orderByDesc('updated_at')
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $user->load('addresses');

        return response()->json([
            'addresses' => $user->addresses,
            'message' => 'Address deleted',
        ]);
    }

    public function userUpdate(User $user, Address $address, UpdateUserRequest $request): JsonResponse
    {
        $address->update($request->validated());

        return response()->json([
            'addresses' => $user->addresses,
            'message' => 'Address updated',
        ]);
    }
}
