<?php

namespace App\Policies;

use App\Models\User;

class CalendarPolicy
{
    public function edit(User $user): bool
    {
        return $user->can('calendars.edit');
    }

    public function view(User $user): bool
    {
        return $user->can('calendars.view');
    }
}
