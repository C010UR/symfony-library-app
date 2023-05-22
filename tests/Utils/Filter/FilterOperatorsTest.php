<?php

namespace App\Tests\Utils\Filter;

use App\Utils\Filter\FilterOperators;
use PHPUnit\Framework\TestCase;

class FilterOperatorsTest extends TestCase
{
    public function getAllOperators(): iterable
    {
        yield 'All' => [
            [
                'Equals To' => 'eq',
                'Greater Than' => 'gt',
                'Less Than' => 'lt',
                'Greater Or Equal To' => 'gte',
                'Less Or Equal To' => 'lte',
                'Not Equals To' => 'neq',
                'Is Null' => 'null',
                'Is Not null' => 'not-null',
                'In' => 'in',
                'Not In' => 'not-in',
                'Contains' => 'contains',
                'Starts With' => 'starts-with',
                'Ends With' => 'ends-with',
                'Between' => 'between',
            ],
        ];
    }

    public function getOperatorsForNumberTypes(): iterable
    {
        $operators = [
            'Equals To' => 'eq',
            'Not Equals To' => 'neq',
            'Greater Than' => 'gt',
            'Less Than' => 'lt',
            'Greater Or Equal To' => 'gte',
            'Less Or Equal To' => 'lte',
            'In' => 'in',
            'Not In' => 'not-in',
            'Between' => 'between',
        ];

        yield 'Without null' => [$operators, false];

        $operators['Is Null'] = 'null';
        $operators['Is Not Null'] = 'not-null';

        yield 'With null' => [$operators, true];
    }

    public function getOperatorsForStringTypes(): iterable
    {
        $operators = [
            'Equals To' => 'eq',
            'Not Equals To' => 'neq',
            'In' => 'in',
            'Not In' => 'not-in',
            'Contains' => 'contains',
            'Starts With' => 'starts-with',
            'Ends With' => 'ends-with',
        ];

        yield 'Without null' => [$operators, false];

        $operators['Is Null'] = 'null';
        $operators['Is Not Null'] = 'not-null';

        yield 'With null' => [$operators, true];
    }

    public function getOperatorsForDateTypes(): iterable
    {
        $operators = [
            'Equals To' => 'eq',
            'Not Equals To' => 'neq',
            'Greater Or Equal To' => 'gte',
            'Less Or Equal To' => 'lte',
            'Between' => 'between',
        ];

        yield 'Without null' => [$operators, false];

        $operators['Is Null'] = 'null';
        $operators['Is Not Null'] = 'not-null';

        yield 'With null' => [$operators, true];
    }

    public function getOperatorsForBoolTypes(): iterable
    {
        $operators = [
            'Equals To' => 'eq',
            'Not Equals To' => 'neq',
        ];

        yield 'Without null' => [$operators, false];

        $operators['Is Null'] = 'null';
        $operators['Is Not Null'] = 'not-null';

        yield 'With null' => [$operators, true];
    }

    public function getOperatorsForEntityTypes(): iterable
    {
        $operators = [
            'Equals To' => 'eq',
            'Not Equals To' => 'neq',
            'In' => 'in',
            'Not In' => 'not-in',
        ];

        yield 'Without null' => [$operators, false];

        $operators['Is Null'] = 'null';
        $operators['Is Not Null'] = 'not-null';

        yield 'With null' => [$operators, true];
    }

    /**
     * @dataProvider getAllOperators
     */
    public function testGetAll(array $expected): void
    {
        $actual = FilterOperators::getAll();

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider getOperatorsForNumberTypes
     */
    public function testGetOperatorsForNumberTypes(array $expected, bool $isNullable): void
    {
        $actual = FilterOperators::getForNumberTypes($isNullable);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider getOperatorsForStringTypes
     */
    public function testGetOperatorsForStringTypes(array $expected, bool $isNullable): void
    {
        $actual = FilterOperators::getForStringTypes($isNullable);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider getOperatorsForDateTypes
     */
    public function testGetOperatorsForDateTypes(array $expected, bool $isNullable): void
    {
        $actual = FilterOperators::getForDateTypes($isNullable);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider getOperatorsForBoolTypes
     */
    public function testGetOperatorsForBoolTypes(array $expected, bool $isNullable): void
    {
        $actual = FilterOperators::getForBoolTypes($isNullable);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @dataProvider getOperatorsForEntityTypes
     */
    public function testGetOperatorsForEntityTypes(array $expected, bool $isNullable): void
    {
        $actual = FilterOperators::getForEntityTypes($isNullable);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }
}
