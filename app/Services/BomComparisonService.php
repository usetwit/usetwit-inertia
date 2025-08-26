<?php

namespace App\Services;

use App\Models\BomVersion;
use Illuminate\Support\Collection;

class BomComparisonService
{
    public function networkHasChanged(BomVersion $bomVersion, array $newData): bool
    {
        $existingNetwork = $this->getOperationNetwork($bomVersion);

        $newNetwork = $this->buildOperationNetwork($newData['operations']);

        return $existingNetwork->toJson() !== $newNetwork->toJson();
    }

    /**
     * Get the current operation network (successors relationships) from the database.
     */
    protected function getOperationNetwork(BomVersion $bomVersion): Collection
    {
        return $bomVersion->bomOperations()
            ->with('successors')
            ->get()
            ->sortKeys()
            ->mapWithKeys(fn($operation) => [
                $operation->id => $operation->successors->pluck('id')->sort()->values(),
            ]);
    }

    /**
     * Build the new operation network from the incoming request data.
     */
    protected function buildOperationNetwork(array $operations): Collection
    {
        return collect($operations)
            ->sortKeys()
            ->mapWithKeys(fn($op) => [
                $op['id'] => collect($op['successors'] ?? [])->sort()->values(),
            ]);
    }
}
