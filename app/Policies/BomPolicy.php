<?php

namespace App\Policies;

use App\Models\Bom;
use App\Models\User;

class BomPolicy
{
    public function view(User $user): bool
    {
        return $user->can('boms.view');
    }

    public function update(User $user, Bom $bom): bool
    {
        return $user->can('boms.update') || $user->can('boms.update.self') && $user->id === $bom->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('boms.create');
    }
}
