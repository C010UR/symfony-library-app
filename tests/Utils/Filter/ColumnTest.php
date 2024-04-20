<?php

namespace App\Tests\Utils\Filter;

use App\Utils\Filter\Column;
use App\Utils\Filter\FilterOperators;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Column::class)]
class ColumnTest extends TestCase
{
    public static function getTypes(): iterable
    {
        $data = [
            [Column::BOOLEAN_TYPE, 'getForBoolTypes'],
            [Column::INTEGER_TYPE, 'getForNumberTypes'],
            [Column::FLOAT_TYPE, 'getForNumberTypes'],
            [Column::STRING_TYPE, 'getForStringTypes'],
            [Column::ENTITY_TYPE, 'getForEntityTypes'],
            [Column::ENTITIES_TYPE, 'getForEntitiesTypes'],
            [Column::DATE_TYPE, 'getForDateTypes'],
        ];

        foreach ($data as $value) {
            yield sprintf('%s not nullable', $value[0]) => [
                $value[0],
                false,
                call_user_func([FilterOperators::class, $value[1]], false),
            ];

            yield sprintf('%s nullable', $value[0]) => [
                $value[0],
                true,
                call_user_func([FilterOperators::class, $value[1]], true),
            ];
        }

        yield 'Non filterable' => [Column::NOT_FILTERABLE_TYPE, false, []];
    }

    public static function getOperators(): iterable
    {
        yield 'Integer - eq' => [Column::INTEGER_TYPE, 'eq', true];
        yield 'Integer - eqa' => [Column::INTEGER_TYPE, 'eqa', false];
        yield 'String - contains' => [Column::STRING_TYPE, 'contains', true];
        yield 'String - starts-with' => [Column::STRING_TYPE, 'starts-with', true];
        yield 'Entity - in' => [Column::ENTITY_TYPE, 'in', true];
        yield 'Entity - in-out' => [Column::ENTITY_TYPE, 'in-out', false];
        yield 'Entities - in' => [Column::ENTITIES_TYPE, 'in', true];
    }

    public static function getConvert(): iterable
    {
        $data = [
            [Column::BOOLEAN_TYPE, [true, false, true]],
            [Column::INTEGER_TYPE, [123, 231, 321]],
            [Column::FLOAT_TYPE, [1.23, 2.31, 3.21]],
            [Column::STRING_TYPE, ['string', 'str', 'srt']],
            [Column::ENTITY_TYPE, [123, 231, 321], 'test'],
            [Column::ENTITIES_TYPE, [123, 231, 321], 'test'],
        ];

        foreach ($data as $value) {
            yield sprintf('%s not array', $value[0]) => [
                new Column([
                    'name' => 'test',
                    'label' => 'test',
                    'type' => $value[0],
                    'isOrderable' => false,
                    'isNullable' => false,
                    'entity' => $value[2] ?? null,
                ]),
                (string) $value[1][0],
                false,
                $value[1][0],
            ];

            yield sprintf('%s array', $value[0]) => [
                new Column([
                    'name' => 'test',
                    'label' => 'test',
                    'type' => $value[0],
                    'isOrderable' => false,
                    'isNullable' => false,
                    'entity' => $value[2] ?? null,
                ]),
                implode(',', $value[1]),
                true,
                $value[1],
            ];
        }

        $dateString = (new \DateTime())->format(\DateTime::ATOM);
        $date = new \DateTime($dateString);

        yield 'Date not array' => [
            new Column([
                'name' => 'test',
                'label' => 'test',
                'type' => Column::DATE_TYPE,
                'isOrderable' => false,
                'isNullable' => false,
            ]),
            $dateString,
            false,
            $date,
        ];

        yield 'Date array' => [
            new Column([
                'name' => 'test',
                'label' => 'test',
                'type' => Column::DATE_TYPE,
                'isOrderable' => false,
                'isNullable' => false,
            ]),
            implode(',', [$dateString, $dateString]),
            true,
            [$date, $date],
        ];
    }

    #[DataProvider('getTypes')]
    public function testSetType(string $type, bool $isNullable, array $expected): void
    {
        $column = new Column([
            'name' => 'test',
            'label' => 'test',
            'type' => $type,
            'isOrderable' => true,
            'isNullable' => $isNullable,
            'entity' => Column::ENTITIES_TYPE === $type || Column::ENTITY_TYPE === $type ? 'test' : null,
        ]);

        $this->assertEquals($expected, $column->getOperators());
    }

    #[DataProvider('getTypes')]
    public function testSetTypeFail(string $type, bool $isNullable, array $expected): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Column([
            'name' => 'test',
            'label' => 'test',
            'type' => Column::ENTITIES_TYPE === $type || Column::ENTITY_TYPE === $type ? $type : sprintf('%s-test', $type),
            'isOrderable' => true,
            'isNullable' => $isNullable,
        ]);
    }

    #[DataProvider('getOperators')]
    public function testIsValidOperator(string $type, string $operator, bool $expected): void
    {
        $column = new Column([
            'name' => 'test',
            'label' => 'test',
            'type' => $type,
            'isOrderable' => true,
            'isNullable' => true,
            'entity' => 'test',
        ]);

        $this->assertEquals($expected, $column->isValidOperator($operator));
    }

    #[DataProvider('getConvert')]
    public function testConvert(Column $column, string $data, bool $isArray, mixed $expected): void
    {
        $actual = Column::convert($column, $data, $isArray);

        $this->assertEquals($expected, $actual);
    }
}
