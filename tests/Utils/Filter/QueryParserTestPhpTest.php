<?php

namespace App\Tests\Utils\Filter;

use App\Utils\Filter\Column;
use App\Utils\Filter\Exception\InvalidQueryExpressionException;
use App\Utils\Filter\Exception\InvalidQueryOrderException;
use App\Utils\Filter\QueryParser;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryParser::class)]
class QueryParserTestPhpTest extends TestCase
{
    public static function getColumns(): iterable
    {
        yield 'columns' => [
            [
                new Column([
                    'name' => 'test-1',
                    'label' => 'label-1',
                    'type' => Column::INTEGER_TYPE,
                    'isOrderable' => true,
                    'isNullable' => false,
                ]),
                new Column([
                    'name' => 'test-2',
                    'label' => 'label-2',
                    'type' => Column::FLOAT_TYPE,
                    'isOrderable' => false,
                    'isNullable' => false,
                ]),
                new Column([
                    'name' => 'test-3',
                    'label' => 'label-3',
                    'type' => Column::STRING_TYPE,
                    'isOrderable' => true,
                    'isNullable' => true,
                ]),
                new Column([
                    'name' => 'test-4',
                    'label' => 'label-4',
                    'type' => Column::ENTITY_TYPE,
                    'isOrderable' => false,
                    'isNullable' => true,
                    'entity' => 'test',
                ]),
                new Column([
                    'name' => 'test-5',
                    'label' => 'label-5',
                    'type' => Column::BOOLEAN_TYPE,
                    'isOrderable' => true,
                    'isNullable' => false,
                ]),
                new Column([
                    'name' => 'test-6',
                    'label' => 'label-6',
                    'type' => Column::NOT_FILTERABLE_TYPE,
                    'isOrderable' => true,
                    'isNullable' => true,
                ]),
                new Column([
                    'name' => 'test-7',
                    'label' => 'label-7',
                    'type' => Column::ENTITIES_TYPE,
                    'isOrderable' => false,
                    'isNullable' => true,
                    'entity' => 'test',
                ]),
            ],
        ];
    }

    public static function getWrongColumns(): iterable
    {
        yield 'columns' => [213, 'super-wrong-yeah', new \ReflectionClass(\InvalidArgumentException::class)];
    }

    public static function getPaginations(): iterable
    {
        yield 'No pagination' => [null, null, Criteria::create()];
        yield 'Set max results' => [10, null, Criteria::create()->setMaxResults(10)];
        yield 'Set first result' => [null, 10, Criteria::create()->setFirstResult(10)];
        yield 'Wrong values' => [-1, -1, Criteria::create()];
        yield 'Set max results and first result' => [
            10,
            10,
            Criteria::create()
                ->setFirstResult(10)
                ->setMaxResults(10),
        ];
    }

    public static function getOrderings(): iterable
    {
        $columns = [
            new Column([
                'name' => 'test-1',
                'label' => 'test',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => true,
                'isNullable' => false,
            ]),
            new Column([
                'name' => 'test-2',
                'label' => 'test',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => true,
                'isNullable' => false,
            ]),
        ];

        yield 'Order asc 1' => [$columns, ['test-1' => 'ASC'], Criteria::create()->orderBy(['test-1' => 'ASC'])];

        yield 'Order asc 2' => [
            $columns,
            ['test-1' => 'ASC', 'test-2' => 'ASC'],
            Criteria::create()->orderBy(['test-1' => 'ASC', 'test-2' => 'ASC']),
        ];

        yield 'Order asc and desc' => [
            $columns,
            ['test-1' => 'ASC', 'test-2' => 'DESC'],
            Criteria::create()->orderBy(['test-1' => 'ASC', 'test-2' => 'DESC']),
        ];
    }

    public static function getWrongOrderings(): iterable
    {
        $columns = [
            new Column([
                'name' => 'test-1',
                'label' => 'test',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => true,
                'isNullable' => false,
            ]),
            new Column([
                'name' => 'test-2',
                'label' => 'test',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => false,
                'isNullable' => false,
            ]),
        ];

        yield 'Wrong column' => [$columns, ['test-3' => 'ASC']];

        yield 'Not orderable' => [$columns, ['test-2' => 'ASC']];

        yield 'Wrong order direction' => [$columns, ['test-1' => 'ASCEND']];
    }

    public static function getFilters(): iterable
    {
        $columns = [
            new Column([
                'name' => 'test-1',
                'label' => 'test',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => true,
                'isNullable' => true,
            ]),
            new Column([
                'name' => 'test-2',
                'label' => 'test',
                'type' => Column::STRING_TYPE,
                'isOrderable' => true,
                'isNullable' => false,
            ]),
        ];

        yield 'NOT NULL' => [
            $columns,
            ['test-1' => ['null' => '']],
            Criteria::create()->andWhere(Criteria::expr()->neq('test-1', null)),
        ];

        yield 'EQUALS TO' => [
            $columns,
            ['test-1' => ['eq' => '2']],
            Criteria::create()->andWhere(Criteria::expr()->eq('test-1', 2)),
        ];

        yield 'NOT EQUALS TO' => [
            $columns,
            ['test-1' => ['neq' => '2']],
            Criteria::create()->andWhere(Criteria::expr()->neq('test-1', 2)),
        ];

        yield 'GREATER THAN' => [
            $columns,
            ['test-1' => ['gt' => '2']],
            Criteria::create()->andWhere(Criteria::expr()->gt('test-1', 2)),
        ];

        yield 'GREATER THAN OR EQUALS TO' => [
            $columns,
            ['test-1' => ['gte' => '2']],
            Criteria::create()->andWhere(Criteria::expr()->gte('test-1', 2)),
        ];

        yield 'LESS THAN' => [
            $columns,
            ['test-1' => ['lt' => '2']],
            Criteria::create()->andWhere(Criteria::expr()->lt('test-1', 2)),
        ];

        yield 'LESS THAN OR EQUALS TO' => [
            $columns,
            ['test-1' => ['lte' => '2']],
            Criteria::create()->andWhere(Criteria::expr()->lte('test-1', 2)),
        ];

        yield 'IN' => [
            $columns,
            ['test-1' => ['in' => '2,3,4']],
            Criteria::create()->andWhere(Criteria::expr()->in('test-1', [2, 3, 4])),
        ];

        yield 'NOT IN' => [
            $columns,
            ['test-1' => ['not-in' => '2,3,4']],
            Criteria::create()->andWhere(Criteria::expr()->notIn('test-1', [2, 3, 4])),
        ];

        yield 'CONTAINS' => [
            $columns,
            ['test-2' => ['contains' => 'str']],
            Criteria::create()->andWhere(Criteria::expr()->contains('test-2', 'str')),
        ];

        yield 'STARTS WITH' => [
            $columns,
            ['test-2' => ['starts-with' => 'str']],
            Criteria::create()->andWhere(Criteria::expr()->startsWith('test-2', 'str')),
        ];

        yield 'ENDS WITH' => [
            $columns,
            ['test-2' => ['ends-with' => 'str']],
            Criteria::create()->andWhere(Criteria::expr()->endsWith('test-2', 'str')),
        ];

        yield 'BETWEEN' => [
            $columns,
            ['test-1' => ['between' => '1,2']],
            Criteria::create()->andWhere(
                Criteria::expr()->andX(Criteria::expr()->gte('test-1', 1), Criteria::expr()->lte('test-1', 2)),
            ),
        ];
    }

    public static function getWrongFilters(): iterable
    {
        $columns = [
            new Column([
                'name' => 'test-1',
                'label' => 'test',
                'type' => Column::NOT_FILTERABLE_TYPE,
                'isOrderable' => true,
                'isNullable' => true,
            ]),
            new Column([
                'name' => 'test-2',
                'label' => 'test',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => true,
                'isNullable' => false,
            ]),
            new Column([
                'name' => 'test-3',
                'label' => 'test',
                'type' => Column::DATE_TYPE,
                'isOrderable' => true,
                'isNullable' => false,
            ]),
        ];

        yield 'Invalid column' => [$columns, ['test-4' => ['eq' => '2']]];

        yield 'Invalid expression' => [$columns, ['test-1' => ['eq' => '2']]];

        yield 'Invalid expression 2' => [$columns, ['test-2' => ['equal' => '2']]];

        yield 'Not convertable' => [$columns, ['test-3' => ['eq' => '*&!^&#*^$AASlkd']]];

        yield 'Invalid between' => [$columns, ['test-2' => ['between' => '1,2,3,4,5,6']]];
    }

    #[DataProvider('getColumns')]
    public function testSetAllowedColumns(array $columns): void
    {
        $queryParser = new QueryParser();
        $queryParser->setColumns($columns);

        $expected = [];
        foreach ($columns as $column) {
            $expected[] = $column->getAll();
        }

        $this->assertEquals($expected, $queryParser->getColumns());
    }

    #[DataProvider('getColumns')]
    public function testSetAllowedColumn(array $columns): void
    {
        foreach ($columns as $column) {
            $queryParser = new QueryParser();
            $queryParser->setColumns($column);

            $this->assertEquals([$column->getAll()], $queryParser->getColumns());
        }
    }

    #[DataProvider('getWrongColumns')]
    public function testSetAllowedColumnFail(mixed $column): void
    {
        $queryParser = new QueryParser();

        $this->expectException(\InvalidArgumentException::class);
        $queryParser->setColumns([$column]);
    }

    #[DataProvider('getPaginations')]
    public function testPagination(?int $maxResults, ?int $firstResult, Criteria $expected): void
    {
        $queryParser = new QueryParser();
        $actual = $queryParser->parseQuery(
            [
                'pageSize' => $maxResults,
                'offset' => $firstResult,
            ],
            true,
        );

        $this->assertEquals($expected, $actual);
    }

    #[DataProvider('getOrderings')]
    public function testOrderings(array $columns, array $orderings, Criteria $expected): void
    {
        $queryParser = new QueryParser();
        $queryParser->setColumns($columns);

        $actual = $queryParser->parseQuery(['order' => $orderings], false, true);

        $this->assertEquals($expected, $actual);
    }

    #[DataProvider('getWrongOrderings')]
    public function testWrongOrderings(array $columns, array $orderings): void
    {
        $queryParser = new QueryParser();
        $queryParser->setColumns($columns);

        $this->expectException(InvalidQueryOrderException::class);

        $queryParser->parseQuery(['order' => $orderings], false, true);
    }

    #[DataProvider('getFilters')]
    public function testFilters(array $columns, array $filters, Criteria $expected): void
    {
        $queryParser = new QueryParser();
        $queryParser->setColumns($columns);

        $actual = $queryParser->parseQuery($filters);

        $this->assertEquals($expected, $actual);
    }

    #[DataProvider('getWrongFilters')]
    public function testFiltersFail(array $columns, array $filters): void
    {
        $queryParser = new QueryParser();
        $queryParser->setColumns($columns);

        $this->expectException(InvalidQueryExpressionException::class);

        $queryParser->parseQuery($filters, true);
    }
}
