<?php

namespace App\Policies;

use App\Models\User;

class CalendarPolicy
{
    public function update(User $user): bool
    {
        return $user->can('calendars.update');
    }

    public function view(User $user): bool
    {
        return $user->can('calendars.view');
    }
}
