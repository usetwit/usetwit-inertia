<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Bom extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        parent::booted();

        static::deleting(function ($model) {
            $model->update(['active' => 0]);
        });

        static::restoring(function ($model) {
            $model->update(['active' => 1]);
        });
    }

    public function bomVersions(): HasMany
    {
        return $this->hasMany(BomVersion::class);
    }

    public function latestVersionNumber(): int
    {
        return $this->bomVersions()->max('version');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(BomVersion::class)->latestOfMany('version');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
