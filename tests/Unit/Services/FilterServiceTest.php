<?php

namespace Services;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Rules\HasMultipleConstraints;
use App\Services\FilterService;
use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mockery;
use Mockery\LegacyMockInterface;
use Tests\TestCase;

class FilterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Builder|LegacyMockInterface $builder;

    protected FilterService $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->mock(Builder::class);
        $this->filter = app(FilterService::class);

        $this->app->instance(BuilderContract::class, $this->builder);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_global_filter_returns_no_results_for_literal_wildcards()
    {
        $this->createTestTable();

        DB::table('test_table')->insert([
            ['name' => 'Hello'],
        ]);

        $query = DB::table('test_table');
        $this->filter->globalFilter($query, '%Hello%', ['name'], ['name']);
        $results = $query->get();
        $this->assertCount(0, $results);

        $query = DB::table('test_table');
        $this->filter->globalFilter($query, '_Hello_', ['name'], ['name']);
        $results = $query->get();
        $this->assertCount(0, $results);
    }

    public function test_global_filter_treats_wildcards_as_literals()
    {
        $this->createTestTable();

        DB::table('test_table')->insert([
            ['name' => 'Hello'],
            ['name' => 'Hello%'],
            ['name' => 'Hello_'],
            ['name' => '\\'],
        ]);

        $query = DB::table('test_table');
        $this->filter->globalFilter($query, 'Hello%', ['name'], ['name']);
        $results = $query->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Hello%', $results->first()->name);

        $query = DB::table('test_table');
        $this->filter->globalFilter($query, 'Hello_', ['name'], ['name']);
        $results = $query->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Hello_', $results->first()->name);

        $query = DB::table('test_table');
        $this->filter->globalFilter($query, '\\', ['name'], ['name']);

        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals('\\', $results->first()->name);
    }

    public function test_sort_method_applies_order_by_correctly(): void
    {
        $this->builder->shouldReceive('orderBy')
            ->with('name', 'asc')
            ->once();
        $this->builder->shouldReceive('orderBy')
            ->with('age', 'desc')
            ->times(2);

        $sort = [
            ['field' => 'name', 'order' => 'asc'],
            ['field' => 'age', 'order' => 'desc'],
            ['field' => 'age', 'order' => 'test'],
        ];

        $this->filter->sort($this->builder, $sort);
    }

    public function test_sort_method_does_not_apply_order_by_when_exceptions_specified(): void
    {
        $this->builder->shouldNotReceive('orderBy');

        $sort = [['field' => 'name', 'order' => 'asc']];
        $exceptions = ['name'];

        $this->filter->sort($this->builder, $sort, $exceptions);
    }

    public function test_sort_method_correctly_substitutes_column_names(): void
    {
        $this->builder->shouldReceive('orderBy')
            ->with('name.test', 'asc')
            ->once();

        $sort = [['field' => 'name', 'order' => 'asc']];
        $substitutes = ['name' => 'name.test'];

        $this->filter->sort($this->builder, $sort, [], $substitutes);
    }

    public function test_make_validation_filter_rules_throws_exception_for_invalid_type(): void
    {
        $this->expectException(FilterServiceGetTypeInvalidException::class);

        $this->filter->makeValidationFilterRules('invalid_type', ['name']);
    }

    public function test_get_match_modes_throws_exception_for_invalid_type(): void
    {
        $this->expectException(FilterServiceGetTypeInvalidException::class);

        $this->filter->getMatchModes('invalid_type');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_get_valid_date_match_modes_returns_correct_modes(): void
    {
        $expected = [
            'date_equals',
            'date_not_equals',
            'date_before',
            'date_after',
            'date_gt',
            'date_gte',
            'date_lt',
            'date_lte',
        ];

        $result = $this->filter->getValidDateMatchModes();

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_get_valid_string_match_modes_as_string(): void
    {
        $expected = 'contains,starts_with,ends_with,equals,not_equals,gt,gte,lt,lte';

        $result = $this->filter->getValidStringMatchModes(true);

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_get_valid_number_match_modes_array(): void
    {
        $expected = ['contains', 'starts_with', 'ends_with', 'equals', 'not_equals', 'gt', 'gte', 'lt', 'lte'];

        $result = $this->filter->getValidNumberMatchModes();

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_get_valid_boolean_match_modes_array(): void
    {
        $expected = ['equals'];

        $result = $this->filter->getValidBooleanMatchModes();

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_contains_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('name', 'like', '%john%')
            ->once();

        $this->filter->queryFilter($this->builder, 'name', 'john', 'contains', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_starts_with_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('name', 'like', 'john%')
            ->once();

        $this->filter->queryFilter($this->builder, 'name', 'john', 'starts_with', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_ends_with_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('name', 'like', '%john')
            ->once();

        $this->filter->queryFilter($this->builder, 'name', 'john', 'ends_with', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_equals_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('name', '=', 'john')
            ->once();

        $this->filter->queryFilter($this->builder, 'name', 'john', 'equals', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_not_equals_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('name', '!=', 'john')
            ->once();

        $this->filter->queryFilter($this->builder, 'name', 'john', 'not_equals', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_gt_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('age', '>', 30)
            ->once();

        $this->filter->queryFilter($this->builder, 'age', 30, 'gt', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_gte_mode(): void
    {
        $this->builder->shouldReceive('where')
            ->with('age', '>=', 30)
            ->once();

        $this->filter->queryFilter($this->builder, 'age', 30, 'gte', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_date_equals_mode(): void
    {
        $this->builder->shouldReceive('whereDate')
            ->with('joined_at', '=', '2023-01-01')
            ->once();

        $this->filter->queryFilter($this->builder, 'joined_at', '2023-01-01', 'date_equals', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_date_not_equals_mode(): void
    {
        $this->builder->shouldReceive('whereDate')
            ->with('joined_at', '!=', '2023-01-01')
            ->once();

        $this->filter->queryFilter($this->builder, 'joined_at', '2023-01-01', 'date_not_equals', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_date_gt_mode(): void
    {
        $this->builder->shouldReceive('whereDate')
            ->with('joined_at', '>', '2023-01-01')
            ->once();

        $this->filter->queryFilter($this->builder, 'joined_at', '2023-01-01', 'date_gt', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_date_gte_mode(): void
    {
        $this->builder->shouldReceive('whereDate')
            ->with('joined_at', '>=', '2023-01-01')
            ->once();

        $this->filter->queryFilter($this->builder, 'joined_at', '2023-01-01', 'date_gte', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_date_lt_mode(): void
    {
        $this->builder->shouldReceive('whereDate')
            ->with('joined_at', '<', '2023-01-01')
            ->once();

        $this->filter->queryFilter($this->builder, 'joined_at', '2023-01-01', 'date_lt', 'and');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_query_filter_applies_date_lte_mode(): void
    {
        $this->builder->shouldReceive('whereDate')
            ->with('joined_at', '<=', '2023-01-01')
            ->once();

        $this->filter->queryFilter($this->builder, 'joined_at', '2023-01-01', 'date_lte', 'and');
    }

    public function test_filter_method_applies_nested_where_or_where_correctly(): void
    {
        $this->builder->shouldReceive('where')
            ->times(2)
            ->with(Mockery::type('callable'))
            ->andReturnUsing(function (callable $callback) {
                $callback($this->builder);
            });

        $this->builder->shouldReceive('orWhere')
            ->once()
            ->with('email', '>', 'john@example.com')
            ->andReturn($this->builder);

        $this->builder->shouldReceive('orWhere')
            ->once()
            ->with('email', 'like', '%lee@example.com%')
            ->andReturn($this->builder);

        $this->builder->shouldReceive('orWhere')
            ->once()
            ->with('username', '=', 'john')
            ->andReturn($this->builder);

        $filters = [
            'username' => [
                'operator' => 'or',
                'constraints' => [
                    ['value' => 'john', 'mode' => 'equals'],
                ],
            ],
            'email' => [
                'operator' => 'or',
                'constraints' => [
                    ['value' => 'john@example.com', 'mode' => 'gt'],
                    ['value' => 'lee@example.com', 'mode' => 'contains'],
                ],
            ],
        ];

        $this->filter->filter($this->builder, $filters);
    }

    public function test_global_filter_applies_correct_queries_based_on_visible_fields_and_substitutions(): void
    {
        $this->builder->shouldReceive('where')
            ->once()
            ->with(Mockery::type('callable'))
            ->andReturnUsing(function (callable $callback) {
                $callback($this->builder);
            });

        $this->builder->shouldReceive('orWhere')
            ->once()
            ->with('users.username', 'like', '%john%')
            ->andReturn($this->builder);

        $this->builder->shouldReceive('orWhere')
            ->once()
            ->with('email', 'like', '%john%')
            ->andReturn($this->builder);

        $this->builder->shouldNotReceive('orWhere')
            ->with('first_name', Mockery::any(), Mockery::any());

        $global = ['username', 'email', 'first_name'];
        $substitutions = ['username' => 'users.username'];
        $visible = ['username', 'email'];

        $this->filter->globalFilter($this->builder, 'john', $global, $visible, $substitutions);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_make_validation_filter_rules_with_string_type(): void
    {
        $constraintType = 'string';
        $fields = ['username'];

        $expectedRules = [
            'filters.username' => 'nullable|array',
            'filters.username.constraints' => 'nullable|array|max:5',
            'filters.username.constraints.*.mode' => [
                'nullable',
                'string',
                Rule::in($this->filter->getValidStringMatchModes()),
                'required_with:filters.username.constraints.*.value',
            ],
            'filters.username.constraints.*.value' => [
                'nullable',
                'present_with:filters.username.constraints.*.mode',
                'string',
            ],
            'filters.username.operator' => [
                'nullable',
                'in:and,or',
                new HasMultipleConstraints(request()->input('filters.username.constraints')),
            ],
        ];

        $result = $this->filter->makeValidationFilterRules($constraintType, $fields);

        $this->assertEquals($expectedRules, $result);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function test_make_validation_filter_rules_with_array_type(): void
    {
        $constraintType = [
            'string' => ['username', 'email'],
            'number' => ['age'],
        ];

        $expectedRules = [
            'filters.username' => 'nullable|array',
            'filters.username.constraints' => 'nullable|array|max:5',
            'filters.username.constraints.*.mode' => [
                'nullable',
                'string',
                Rule::in($this->filter->getValidStringMatchModes()),
                'required_with:filters.username.constraints.*.value',
            ],
            'filters.username.constraints.*.value' => [
                'nullable',
                'present_with:filters.username.constraints.*.mode',
                'string',
            ],
            'filters.username.operator' => [
                'nullable',
                'in:and,or',
                new HasMultipleConstraints(request()->input('filters.username.constraints')),
            ],
            'filters.email' => 'nullable|array',
            'filters.email.constraints' => 'nullable|array|max:5',
            'filters.email.constraints.*.mode' => [
                'nullable',
                'string',
                Rule::in($this->filter->getValidStringMatchModes()),
                'required_with:filters.email.constraints.*.value',
            ],
            'filters.email.constraints.*.value' => [
                'nullable',
                'present_with:filters.email.constraints.*.mode',
                'string',
            ],
            'filters.email.operator' => [
                'nullable',
                'in:and,or',
                new HasMultipleConstraints(request()->input('filters.email.constraints')),
            ],
            'filters.age' => 'nullable|array',
            'filters.age.constraints' => 'nullable|array|max:5',
            'filters.age.constraints.*.mode' => [
                'nullable',
                'string',
                Rule::in($this->filter->getValidNumberMatchModes()),
                'required_with:filters.age.constraints.*.value',
            ],
            'filters.age.constraints.*.value' => [
                'nullable',
                'present_with:filters.age.constraints.*.mode',
                'numeric',
            ],
            'filters.age.operator' => [
                'nullable',
                'in:and,or',
                new HasMultipleConstraints(request()->input('filters.age.constraints')),
            ],
        ];

        $result = $this->filter->makeValidationFilterRules($constraintType);

        $this->assertEquals($expectedRules, $result);
    }
}
