<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public $casts = [
        'default_image' => 'boolean',
    ];

    protected array $validImageables = [
        User::class,
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getValidImageables(): array
    {
        return $this->validImageables;
    }

    /**
     * Scope a query to only include popular users.
     */
    public function scopeDefault(Builder $query): void
    {
        $query->where('default_image', true);
    }
}
