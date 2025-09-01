<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Calendar extends Model
{
    use HasFactory;

    public function shifts(): HasMany
    {
        return $this->hasMany(CalendarShift::class);
    }

    public static array $validCalendarables = [
        Location::class,
        Shift::class,
    ];

    public function calendarable(): MorphTo
    {
        return $this->morphTo();
    }
}
