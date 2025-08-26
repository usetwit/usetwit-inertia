<?php

namespace App\Policies;

use App\Models\User;

class BomPolicy
{
    public function view(User $user): bool
    {
        return $user->can('boms.view');
    }

    public function viewAny(User $user): bool
    {
        return $user->can('boms.view') || $user->can('boms.edit');
    }

    public function edit(User $user): bool
    {
        return $user->can('boms.edit');
    }

    public function create(User $user): bool
    {
        return $user->can('boms.create');
    }
}
