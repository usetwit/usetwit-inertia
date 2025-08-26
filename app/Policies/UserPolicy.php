<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view') || $user->can('users.edit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->can('users.view')) {
            return true;
        }

        if ($user->can('users.view.self')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create an address.
     */
    public function createAddress(User $user, User $model): bool
    {
        if ($user->can('addresses.user.create')) {
            return true;
        }

        if ($user->can('addresses.user.create.self')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create an address.
     */
    public function deleteAddress(User $user, User $model): bool
    {
        if ($user->can('addresses.user.delete')) {
            return true;
        }

        if ($user->can('addresses.user.delete.self')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can edit an address.
     */
    public function editAddress(User $user): bool
    {
        return $user->can('addresses.user.edit') || $user->can('addresses.user.edit.self');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function edit(User $user, User $model): bool
    {
        return $user->can('users.edit')
            || $user->can('editPersonalProfile', $model)
            || $user->can('editAddress', $model)
            || $user->can('editProfileImage', $model)
            || $user->can('editCompanyProfile', $model);
    }

    /**
     * Determine whether the user can edit the model profile.
     */
    public function editPersonalProfile(User $user, User $model): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        if ($user->can('users.edit.self.personal-profile')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model profile.
     */
    public function editCompanyProfile(User $user, User $model): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        if ($user->can('users.edit.self.company-profile')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model profile image.
     */
    public function editProfileImage(User $user, User $model): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        if ($user->can('users.edit.self.profile-image')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model profile image.
     */
    public function editPassword(User $user, User $model): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can override a user's password.
     */
    public function overridePassword(User $user): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model Username.
     */
    public function editUsername(User $user): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model Employee ID.
     */
    public function editEmployeeId(User $user): bool
    {
        if ($user->can('users.edit')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can edit the model protected info.
     */
    public function editProtectedInfo(User $user): bool
    {
        return $user->can('users.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->can('users.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->can('users.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
