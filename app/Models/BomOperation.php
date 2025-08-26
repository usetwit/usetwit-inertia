<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BomOperation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static array $operationTypes = ['process', 'buffer'];

    public static array $bufferDurationTypes = ['minutes', 'calendar_day', 'working_day'];

    public function bomVersion(): BelongsTo
    {
        return $this->belongsTo(BomVersion::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    public function successors(): BelongsToMany
    {
        return $this->belongsToMany(
            BomOperation::class,
            'bom_operation_successors',
            'bom_operation_id',
            'successor_id'
        );
    }

    /**
     * Get the predecessor operations for this operation.
     */
    public function predecessors(): BelongsToMany
    {
        return $this->belongsToMany(
            BomOperation::class,
            'bom_operation_successors',
            'successor_id',
            'bom_operation_id'
        );
    }
}
