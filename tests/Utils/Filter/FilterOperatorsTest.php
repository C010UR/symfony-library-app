<?php

namespace App\Tests\Utils\Filter;

use App\Utils\Filter\FilterOperators;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilterOperators::class)]
class FilterOperatorsTest extends TestCase
{
    public static function getAllOperators(): iterable
    {
        yield 'All' => [
            FilterOperators::format(
                [
                    FilterOperators::OPERATOR_EQUALS_TO,
                    FilterOperators::OPERATOR_GREATER_THAN,
                    FilterOperators::OPERATOR_LESS_THAN,
                    FilterOperators::OPERATOR_GREATER_OR_EQUAL_TO,
                    FilterOperators::OPERATOR_LESS_OR_EQUAL_TO,
                    FilterOperators::OPERATOR_NOT_EQUALS_TO,
                    FilterOperators::OPERATOR_IS_NULL,
                    FilterOperators::OPERATOR_IN,
                    FilterOperators::OPERATOR_NOT_IN,
                    FilterOperators::OPERATOR_CONTAINS,
                    FilterOperators::OPERATOR_STARTS_WITH,
                    FilterOperators::OPERATOR_ENDS_WITH,
                    FilterOperators::OPERATOR_BETWEEN,
                ]
            ),
        ];
    }

    public static function getOperators(): iterable
    {
        $operators = [
            FilterOperators::OPERATOR_EQUALS_TO,
            FilterOperators::OPERATOR_NOT_EQUALS_TO,
            FilterOperators::OPERATOR_GREATER_THAN,
            FilterOperators::OPERATOR_LESS_THAN,
            FilterOperators::OPERATOR_GREATER_OR_EQUAL_TO,
            FilterOperators::OPERATOR_LESS_OR_EQUAL_TO,
            FilterOperators::OPERATOR_IN,
            FilterOperators::OPERATOR_NOT_IN,
            FilterOperators::OPERATOR_BETWEEN,
        ];

        yield 'Not nullable number' => [FilterOperators::format($operators), 'getForNumberTypes', false];

        $operators[] = FilterOperators::OPERATOR_IS_NULL;

        yield 'Nullable number' => [FilterOperators::format($operators), 'getForNumberTypes', true];

        $operators = [
            FilterOperators::OPERATOR_EQUALS_TO,
            FilterOperators::OPERATOR_NOT_EQUALS_TO,
            FilterOperators::OPERATOR_IN,
            FilterOperators::OPERATOR_NOT_IN,
            FilterOperators::OPERATOR_CONTAINS,
            FilterOperators::OPERATOR_STARTS_WITH,
            FilterOperators::OPERATOR_ENDS_WITH,
        ];

        yield 'Not nullable string' => [FilterOperators::format($operators), 'getForStringTypes', false];

        $operators[] = FilterOperators::OPERATOR_IS_NULL;

        yield 'Nullable string' => [FilterOperators::format($operators), 'getForStringTypes', true];

        $operators = [
            FilterOperators::OPERATOR_EQUALS_TO,
            FilterOperators::OPERATOR_NOT_EQUALS_TO,
            FilterOperators::OPERATOR_GREATER_OR_EQUAL_TO,
            FilterOperators::OPERATOR_LESS_OR_EQUAL_TO,
            FilterOperators::OPERATOR_BETWEEN,
        ];

        yield 'Not nullable data' => [FilterOperators::format($operators), 'getForDateTypes', false];

        $operators[] = FilterOperators::OPERATOR_IS_NULL;

        yield 'Nullable data' => [FilterOperators::format($operators), 'getForDateTypes', true];

        $operators = [
            FilterOperators::OPERATOR_EQUALS_TO,
        ];

        yield 'Not nullable bool' => [FilterOperators::format($operators), 'getForBoolTypes', false];

        $operators[] = FilterOperators::OPERATOR_IS_NULL;

        yield 'Nullable bool' => [FilterOperators::format($operators), 'getForBoolTypes', true];

        $operators = [
            FilterOperators::OPERATOR_EQUALS_TO,
            FilterOperators::OPERATOR_NOT_EQUALS_TO,
            FilterOperators::OPERATOR_IN,
            FilterOperators::OPERATOR_NOT_IN,
        ];

        yield 'Not nullable entity' => [FilterOperators::format($operators), 'getForEntityTypes', false];

        $operators[] = FilterOperators::OPERATOR_IS_NULL;

        yield 'Nullable entity' => [FilterOperators::format($operators), 'getForEntityTypes', true];

        $operators = [
            FilterOperators::OPERATOR_IN,
            FilterOperators::OPERATOR_NOT_IN,
        ];

        yield 'Not nullable' => [FilterOperators::format($operators), 'getForEntitiesTypes', false];

        $operators[] = FilterOperators::OPERATOR_IS_NULL;

        yield 'Nullable' => [FilterOperators::format($operators), 'getForEntitiesTypes', true];
    }


    public static function getFormattedOperators(): iterable
    {
        $labels = [
            FilterOperators::OPERATOR_EQUALS_TO => 'Равняется',
            FilterOperators::OPERATOR_NOT_EQUALS_TO => 'Не равняется',
            FilterOperators::OPERATOR_GREATER_THAN => 'Больше',
            FilterOperators::OPERATOR_LESS_THAN => 'Меньше',
            FilterOperators::OPERATOR_GREATER_OR_EQUAL_TO => 'Больше либо равно',
            FilterOperators::OPERATOR_LESS_OR_EQUAL_TO => 'Меньше либо равно',
            FilterOperators::OPERATOR_IS_NULL => 'Пустое',
            FilterOperators::OPERATOR_IN => 'Включает',
            FilterOperators::OPERATOR_NOT_IN => 'Не Включает',
            FilterOperators::OPERATOR_CONTAINS => 'Содержит',
            FilterOperators::OPERATOR_STARTS_WITH => 'Начинается на',
            FilterOperators::OPERATOR_ENDS_WITH => 'Заканчивается на',
            FilterOperators::OPERATOR_BETWEEN => 'Между',
        ];

        $result = [];

        foreach ($labels as $operator => $label) {
            $result[$operator] = [
                $operator,
                $label,
            ];
        }

        return $result;
    }

    #[DataProvider('getAllOperators')]
    public function testGetAll(array $expected): void
    {
        $actual = FilterOperators::getAll();

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    #[DataProvider('getOperators')]
    public function testGetOperators(array $expected, string $func, bool $isNullable): void
    {
        $actual = call_user_func([FilterOperators::class, $func], $isNullable);

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    #[DataProvider('getFormattedOperators')]
    public function testFormat(string $operator, string $label)
    {
        $this->assertEqualsCanonicalizing([
            $operator => [
                'operator' => $operator,
                'label' => $label
            ]
        ], FilterOperators::format([$operator]));
    }
}
