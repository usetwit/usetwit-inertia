<?php

namespace App\Policies;

use App\Models\User;

class ShiftPolicy
{
    public function view(User $user): bool
    {
        return $user->can('shifts.view');
    }

    public function edit(User $user): bool
    {
        return $user->can('shifts.edit');
    }

    public function create(User $user): bool
    {
        return $user->can('shifts.create');
    }
}
