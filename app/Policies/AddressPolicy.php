<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;

class AddressPolicy
{
    /**
     * Determine whether the user can edit a User address.
     */
    public function editUserAddress(User $user, Address $address): bool
    {
        if ($user->can('addresses.user.edit')) {
            return true;
        }

        if ($user->can('addresses.user.edit.self')) {
            return $address->addressable instanceof User && $address->addressable->is($user);
        }

        return false;
    }

    /**
     * Determine whether the user can edit a User address.
     */
    public function deleteUserAddress(User $user, Address $address): bool
    {
        if ($user->can('addresses.user.delete')) {
            return true;
        }

        if ($user->can('addresses.user.delete.self')) {
            return $address->addressable instanceof User && $address->addressable->is($user);
        }

        return false;
    }

    /**
     * Determine whether the user can edit a User address.
     */
    public function editCustomerAddress(User|Customer $user, Address $address): bool
    {
        if ($user->can('addresses.customer.edit')) {
            return true;
        }

        if ($user->can('addresses.customer.edit.self')) {
            return $address->addressable instanceof Customer && $address->addressable->is($user);
        }

        return false;
    }
}
