<?php

namespace App\Policies;

use App\Models\User;

class BomVersionPolicy
{
    public function edit(User $user): bool
    {
        return $user->can('bom-versions.edit');
    }

    public function view(User $user): bool
    {
        return $user->can('bom-versions.view');
    }
}
