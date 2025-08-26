<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarShift extends Model
{
    use HasFactory;

    public $casts = [
        'shift_date' => 'date',
        'nwd' => 'boolean',
    ];

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'shift1_start' => TimeCast::class,
            'shift1_end' => TimeCast::class,
            'shift2_start' => TimeCast::class,
            'shift2_end' => TimeCast::class,
            'shift3_start' => TimeCast::class,
            'shift3_end' => TimeCast::class,
            'shift4_start' => TimeCast::class,
            'shift4_end' => TimeCast::class,
        ];
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }
}
