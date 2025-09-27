<?php

namespace Rules;

use App\Rules\AcyclicGraph;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Tests\TestCase;

class AcyclicGraphRuleTest extends TestCase
{
    /**
     * Helper method to validate operations using AcyclicGraph rule.
     */
    protected function validateOperations(array $operations): Validator
    {
        return ValidatorFacade::make(
            ['operations' => $operations],
            ['operations' => [new AcyclicGraph]]
        );
    }

    public function test_valid_acyclic_graph_passes()
    {
        $operations = [
            ['id' => 1, 'successors' => [2, 3]],
            ['id' => 2, 'successors' => [4]],
            ['id' => 3, 'successors' => []],
            ['id' => 4, 'successors' => []],
        ];

        $validator = $this->validateOperations($operations);

        $this->assertFalse($validator->fails(), 'The validator should pass for an acyclic graph.');
    }

    public function test_cyclic_graph_fails()
    {
        $operations = [
            ['id' => 1, 'successors' => [2]],
            ['id' => 2, 'successors' => [3]],
            ['id' => 3, 'successors' => [1]], // Cycle: 1 -> 2 -> 3 -> 1
        ];

        $validator = $this->validateOperations($operations);

        $this->assertTrue($validator->fails(), 'The validator should fail for a cyclic graph.');
        $this->assertStringContainsString('The operations graph contains a cycle', $validator->errors()
            ->first('operations'));
    }

    public function test_empty_graph_passes()
    {
        $operations = [];

        $validator = $this->validateOperations($operations);

        $this->assertFalse($validator->fails(), 'The validator should pass for an empty graph.');
    }

    public function test_self_loop_fails()
    {
        $operations = [
            ['id' => 1, 'successors' => [1]],
        ];

        $validator = $this->validateOperations($operations);

        $this->assertTrue($validator->fails(), 'The validator should fail for a self-loop.');
        $this->assertStringContainsString(
            'The operations graph contains a cycle',
            $validator->errors()->first('operations')
        );
    }

    public function test_disconnected_acyclic_graph_passes()
    {
        $operations = [
            ['id' => 1, 'successors' => [2]],
            ['id' => 2, 'successors' => []],
            ['id' => 3, 'successors' => [4]], // Separate component
            ['id' => 4, 'successors' => []],
        ];

        $validator = $this->validateOperations($operations);

        $this->assertFalse($validator->fails(), 'The validator should pass for a disconnected acyclic graph.');
    }

    public function test_complex_cycle_fails()
    {
        $operations = [
            ['id' => 1, 'successors' => [2]],
            ['id' => 2, 'successors' => [3, 4]],
            ['id' => 3, 'successors' => [5]],
            ['id' => 4, 'successors' => [6]],
            ['id' => 5, 'successors' => [2]], // Cycle: 2 -> 3 -> 5 -> 2
            ['id' => 6, 'successors' => []],
        ];

        $validator = $this->validateOperations($operations);

        $this->assertTrue($validator->fails(), 'The validator should fail for a complex cyclic graph.');
        $this->assertStringContainsString('The operations graph contains a cycle', $validator->errors()
            ->first('operations'));
    }
}
