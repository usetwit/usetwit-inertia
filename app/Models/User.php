<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements Authorizable
{
    use HasFactory;
    use HasRoles;
    use HasSlug;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['first_name', 'middle_names', 'last_name'])
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

        static::saving(function (self $user) {
            if ($user->isDirty(['first_name', 'middle_names', 'last_name'])) {
                $user->full_name = trim(preg_replace('/\s+/', ' ', "{$user->first_name} {$user->middle_names} {$user->last_name}"));
            }
        });

        static::deleting(function ($model) {
            $model->update(['active' => 0]);
        });

        static::restoring(function ($model) {
            $model->update(['active' => 1]);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'joined_at' => 'date:Y-m-d',
            'left_at' => 'date:Y-m-d',
            'dob' => 'date:Y-m-d',
            'active' => 'boolean',
        ];
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable')
            ->orderByDesc('is_default')
            ->orderByDesc('created_at');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('is_default', true);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function profileImages(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')
            ->whereType('user_profile');
    }

    public function uploadedImages(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class);
    }
}
