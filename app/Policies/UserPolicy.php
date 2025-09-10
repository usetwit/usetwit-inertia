<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->can('users.view');
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
     * Determine whether the user can update an address.
     */
    public function updateAddress(User $user, User $model): bool
    {
        if ($user->can('addresses.user.update')) {
            return true;
        }

        if ($user->can('addresses.user.update.self')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('users.update')
            || $user->can('updatePersonalProfile', $model)
            || $user->can('updateAddress', $model)
            || $user->can('updateProfileImage', $model)
            || $user->can('updateCompanyProfile', $model);
    }

    /**
     * Determine whether the user can update the model profile.
     */
    public function updatePersonalProfile(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.personal-profile')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model profile.
     */
    public function updateCompanyProfile(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.company-profile')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model profile image.
     */
    public function updateProfileImage(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.profile-image')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model profile image.
     */
    public function updatePassword(User $user, User $model): bool
    {
        if ($this->overridePassword($user)) {
            return true;
        }

        if ($user->can('users.update.self.password')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can override a user's password.
     */
    public function overridePassword(User $user): bool
    {
        if ($user->can('users.override.password')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model Username.
     */
    public function updateUsername(User $user): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model Employee ID.
     */
    public function updateEmployeeId(User $user): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model protected info.
     */
    public function updateProtectedInfo(User $user): bool
    {
        return $user->can('users.update.protected-info');
    }

    /**
     * Determine whether the user can update the model role.
     */
    public function updateRole(User $user): bool
    {
        return $user->can('users.update.role');
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
    public function restore(User $user, User $model): bool
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
