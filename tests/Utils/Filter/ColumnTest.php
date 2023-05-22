<?php

namespace App\Tests\Utils\Filter;

use App\Utils\Filter\Column;
use App\Utils\Filter\FilterOperators;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function getTypes(): iterable
    {
        $data = [
            [Column::BOOLEAN_TYPE, 'getForBoolTypes'],
            [Column::INTEGER_TYPE, 'getForNumberTypes'],
            [Column::FLOAT_TYPE, 'getForNumberTypes'],
            [Column::STRING_TYPE, 'getForStringTypes'],
            [Column::ENTITY_TYPE, 'getForEntityTypes'],
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

    public function getOperators(): iterable
    {
        yield 'Integer - eq' => [Column::INTEGER_TYPE, 'eq', true];
        yield 'Integer - eqa' => [Column::INTEGER_TYPE, 'eqa', false];
        yield 'String - contains' => [Column::STRING_TYPE, 'contains', true];
        yield 'String - starts-with' => [Column::STRING_TYPE, 'starts-with', true];
        yield 'Entity - in' => [Column::ENTITY_TYPE, 'in', true];
        yield 'Entity - in-out' => [Column::ENTITY_TYPE, 'in-out', false];
    }

    public function getConvert(): iterable
    {
        $data = [
            [Column::BOOLEAN_TYPE, [true, false, true]],
            [Column::INTEGER_TYPE, [123, 231, 321]],
            [Column::FLOAT_TYPE, [1.23, 2.31, 3.21]],
            [Column::STRING_TYPE, ['string', 'str', 'srt']],
            [Column::ENTITY_TYPE, [123, 231, 321]],
        ];

        foreach ($data as $value) {
            yield sprintf('%s not array', $value[0]) => [
                new Column('test', 'test', $value[0], false, false),
                (string) $value[1][0],
                false,
                $value[1][0],
            ];

            yield sprintf('%s array', $value[0]) => [
                new Column('test', 'test', $value[0], false, false),
                implode(',', $value[1]),
                true,
                $value[1],
            ];
        }

        $dateString = (new DateTimeImmutable())->format(DateTimeImmutable::ATOM);
        $date = new DateTimeImmutable($dateString);

        yield 'Date not array' => [
            new Column('test', 'test', Column::DATE_TYPE, false, false),
            $dateString,
            false,
            $date,
        ];

        yield 'Date array' => [
            new Column('test', 'test', Column::DATE_TYPE, false, false),
            implode(',', [$dateString, $dateString]),
            true,
            [$date, $date],
        ];
    }

    /**
     * @dataProvider getTypes
     */
    public function testSetType(string $type, bool $isNullable, array $expected): void
    {
        $column = new Column('test', 'test', $type, true, $isNullable);

        $this->assertEquals($expected, $column->getOperators());
    }

    /**
     * @dataProvider getTypes
     */
    public function testSetTypeFail(string $type, bool $isNullable, array $expected): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Column('test', 'test', sprintf('%s-test', $type), true, $isNullable);
    }

    /**
     * @dataProvider getOperators
     */
    public function testIsValidOperator(string $type, string $operator, bool $expected): void
    {
        $column = new Column('test', 'test', $type, true, true);

        $this->assertEquals($expected, $column->isValidOperator($operator));
    }

    /**
     * @dataProvider getConvert
     */
    public function testConvert(Column $column, string $data, bool $isArray, mixed $expected): void
    {
        $actual = Column::convert($column, $data, $isArray);

        $this->assertEquals($expected, $actual);
    }
}
