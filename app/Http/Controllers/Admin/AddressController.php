<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Addresses\CreateUserRequest;
use App\Http\Requests\Admin\Addresses\UpdateUserRequest;
use App\Models\Address;
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

    public function makeDefault(Address $address)
    {
        $address->update(['is_default' => true]);

        return back(303)->with('success', 'Address set as default');
    }

    public function userDestroy(Address $address)
    {
        $address->delete();

        if ($address->is_default) {
            $address->addressable
                ->addresses()
                ->orderByDesc('updated_at')
                ->first()
                ?->update(['is_default' => true]);
        }

        return back(303)->with('success', 'Address deleted');
    }

    public function userUpdate(Address $address, UpdateUserRequest $request)
    {
        $address->update($request->validated());

        return back(303)->with('success', 'Address updated');
    }
}
