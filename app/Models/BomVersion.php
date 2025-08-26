<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BomVersion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function bomOperations(): HasMany
    {
        return $this->hasMany(BomOperation::class);
    }
}
