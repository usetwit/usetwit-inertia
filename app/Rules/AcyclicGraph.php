<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AcyclicGraph implements ValidationRule
{
    /**
     * Stores the cycle path if one is found.
     *
     * @var array
     */
    protected array $cyclePath = [];

    /**
     * Validate the graph for cycles.
     *
     * @param string  $attribute
     * @param mixed   $value Expected to be an array of operations.
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $graph = [];
        foreach ($value as $op) {
            $graph[$op['id']] = $op['successors'] ?? [];
        }

        if (! $this->validateGraphCycle($graph)) {
            $cycle = implode(' -> ', $this->cyclePath);
            $fail("The operations graph contains a cycle: $cycle.");
        }
    }

    /**
     * Iterate over each node to check for cycles.
     *
     * @param array $graph
     * @return bool True if acyclic, false if a cycle is detected.
     */
    protected function validateGraphCycle(array $graph): bool
    {
        $visited = [];
        $stack = [];

        foreach (array_keys($graph) as $node) {
            if (! isset($visited[$node])) {
                if ($this->hasCycleDFS($node, $graph, $visited, $stack, [])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Recursive DFS function to detect cycles.
     *
     * @param integer $node
     * @param array   $graph
     * @param array   $visited
     * @param array   $stack
     * @param array   $path
     * @return bool
     */
    protected function hasCycleDFS(int $node, array $graph, array &$visited, array &$stack, array $path): bool
    {
        if (isset($stack[$node])) {
            $path[] = $node;
            $cycleStart = array_search($node, $path);
            $this->cyclePath = array_slice($path, $cycleStart);
            return true;
        }

        if (isset($visited[$node])) {
            return false;
        }

        $visited[$node] = true;
        $stack[$node] = true;
        $path[] = $node;

        if (array_any($graph[$node], fn($nextOp) => $this->hasCycleDFS($nextOp, $graph, $visited, $stack, $path))) {
            return true;
        }

        unset($stack[$node]);

        return false;
    }
}
