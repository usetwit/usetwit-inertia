<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Operation extends Model
{
    use HasFactory;

    public $guarded = [];

    public function calendar(): HasOne
    {
        return $this->hasOne(Calendar::class);
    }
}
