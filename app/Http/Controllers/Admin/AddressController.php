<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Addresses\CreateUserRequest;
use App\Http\Requests\Admin\Addresses\UpdateUserRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AddressController extends Controller
{
    public function userCreate(User $user, CreateUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! $user->addresses()->exists()) {
            $data['is_default'] = true;
        }

        $user->addresses()->create($data);

        return redirect()->back()->with(['success' => 'Address Created Successfully']);
    }

    public function makeDefault(Address $address): RedirectResponse
    {
        $address->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Address Set As Default');
    }

    public function userDestroy(Address $address): RedirectResponse
    {
        $address->delete();

        if ($address->is_default) {
            $address->addressable
                ->addresses()
                ->orderByDesc('updated_at')
                ->first()
                ?->update(['is_default' => true]);
        }

        return redirect()->back()->with('success', 'Address Deleted Successfully');
    }

    public function userUpdate(Address $address, UpdateUserRequest $request): RedirectResponse
    {
        $address->update($request->validated());

        return redirect()->back()->with('success', 'Address Updated Successfully');
    }
}
