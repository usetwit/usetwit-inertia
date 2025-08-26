<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Symfony\Component\Intl\Countries;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public $casts = [
        'is_default' => 'boolean',
    ];

    public static array $validAddressables = [
        Company::class,
        Customer::class,
        User::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Address $model) {
            if ($model->isDirty('country_code')) {
                $model->country_name = Countries::getName($model->country_code, config('app.locale'));
            }

            if ($model->isDirty('is_default') && $model->is_default) {
                $model->addressable
                    ->addresses()
                    ->where('is_default', true)
                    ->whereKeyNot($model->getKey())
                    ->update(['is_default' => false]);
            }
        });

        static::deleting(function ($model) {
            $model->update(['active' => 0]);
        });

        static::restoring(function ($model) {
            $model->update(['active' => 1]);
        });
    }

    protected $appends = [
        'routes',
    ];

    protected function routes(): Attribute
    {
        return Attribute::get(function () {
            $resource = Str::snake(class_basename($this->addressable_type));
            $params = [
                $resource => $this->addressable,
                'address' => $this,
            ];

            return [
                'make_default' => route("admin.addresses.{$resource}.make-default", $params),
                'delete' => route("admin.addresses.{$resource}.destroy", $params),
                'update' => route("admin.addresses.{$resource}.update", $params),
            ];
        });
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
