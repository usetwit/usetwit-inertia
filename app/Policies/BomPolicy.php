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

    public function edit(User $user, Bom $bom): bool
    {
        return $user->can('boms.edit') || $user->can('boms.edit.self') && $user->id === $bom->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('boms.create');
    }
}
