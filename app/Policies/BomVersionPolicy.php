<?php

namespace App\Policies;

use App\Models\User;

class BomVersionPolicy
{
    public function edit(User $user): bool
    {
        return $user->can('boms-bom-versions.edit');
    }
}
