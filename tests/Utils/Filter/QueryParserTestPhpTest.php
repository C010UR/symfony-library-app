<?php

namespace App\Tests\Utils\Filter;

use App\Utils\Filter\Column;
use App\Utils\Filter\Exception\InvalidQueryExpressionException;
use App\Utils\Filter\Exception\InvalidQueryOrderException;
use App\Utils\Filter\QueryParser;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class QueryParserTestPhpTest extends TestCase
{
    public function getColumns(): iterable
    {
        yield 'columns' => [
            [
                new Column('test-1', 'label-1', Column::INTEGER_TYPE, true, false),
                new Column('test-2', 'label-2', Column::FLOAT_TYPE, false, false),
                new Column('test-3', 'label-3', Column::STRING_TYPE, true, true),
                new Column('test-4', 'label-4', Column::ENTITY_TYPE, false, true),
                new Column('test-5', 'label-5', Column::BOOLEAN_TYPE, true, false),
                new Column('test-6', 'label-6', Column::NOT_FILTERABLE_TYPE, true, true),
            ],
        ];
    }

    public function getWrongColumns(): iterable
    {
        yield 'columns' => [213, 'aslkdas', new \ReflectionClass(\InvalidArgumentException::class)];
    }

    public function getPaginations(): iterable
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

    public function getOrderings(): iterable
    {
        $columns = [
            new Column('test-1', 'test', Column::INTEGER_TYPE, true, false),
            new Column('test-2', 'test', Column::INTEGER_TYPE, true, false),
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

    public function getWrongOrderings(): iterable
    {
        $columns = [
            new Column('test-1', 'test', Column::INTEGER_TYPE, true, false),
            new Column('test-2', 'test', Column::INTEGER_TYPE, false, false),
        ];

        yield 'Wrong column' => [$columns, ['test-3' => 'ASC']];

        yield 'Not orderable' => [$columns, ['test-2' => 'ASC']];

        yield 'Wrong order direction' => [$columns, ['test-1' => 'ASCEND']];
    }

    public function getFilters(): iterable
    {
        $columns = [
            new Column('test-1', 'test', Column::INTEGER_TYPE, true, true),
            new Column('test-2', 'test', Column::STRING_TYPE, true, false),
        ];

        yield 'NULL' => [
            $columns,
            ['test-1' => ['null' => '']],
            Criteria::create()->andWhere(Criteria::expr()->isNull('test-1')),
        ];

        yield 'NOT NULL' => [
            $columns,
            ['test-1' => ['not-null' => '']],
            Criteria::create()->andWhere(Criteria::expr()->neq('test-1', 'NULL')),
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

    public function getWrongFilters(): iterable
    {
        $columns = [
            new Column('test-1', 'test', Column::NOT_FILTERABLE_TYPE, true, true),
            new Column('test-2', 'test', Column::INTEGER_TYPE, true, false),
            new Column('test-3', 'test', Column::DATE_TYPE, true, false),
        ];

        yield 'Invalid column' => [$columns, ['test-4' => ['eq' => '2']]];

        yield 'Invalid expression' => [$columns, ['test-1' => ['eq' => '2']]];

        yield 'Invalid expression 2' => [$columns, ['test-2' => ['equal' => '2']]];

        yield 'Not convertable' => [$columns, ['test-3' => ['eq' => '*&!^&#*^$AASlkd']]];

        yield 'Invalid between' => [$columns, ['test-2' => ['between' => '1,2,3,4,5,6']]];
    }

    /**
     * @dataProvider getColumns
     */
    public function testSetAllowedColumns(array $columns): void
    {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns($columns);

        $expected = [];
        foreach ($columns as $column) {
            $expected[] = $column->getAll();
        }

        $this->assertEquals($expected, $queryParser->getAllowedColumns());
    }

    /**
     * @dataProvider getColumns
     */
    public function testSetAllowedColumn(array $columns): void
    {
        foreach ($columns as $column) {
            $queryParser = new QueryParser();
            $queryParser->setAllowedColumns($column);

            $this->assertEquals([$column->getAll()], $queryParser->getAllowedColumns());
        }
    }

    /**
     * @dataProvider getWrongColumns
     */
    public function testSetAllowedColumnFail(mixed $column): void
    {
        $queryParser = new QueryParser();

        $this->expectException(\InvalidArgumentException::class);
        $queryParser->setAllowedColumns([$column]);
    }

    /**
     * @dataProvider getPaginations
     */
    public function testPagination(?int $maxResults, ?int $firstResult, Criteria $expected): void
    {
        $queryParser = new QueryParser();
        $actual = $queryParser->parseQuery(
            [
                'page_size' => $maxResults,
                'offset' => $firstResult,
            ],
            true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getOrderings
     */
    public function testOrderings(array $columns, array $orderings, Criteria $expected): void
    {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns($columns);

        $actual = $queryParser->parseQuery(['order' => $orderings], false, true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getWrongOrderings
     */
    public function testWrongOrderings(array $columns, array $orderings): void
    {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns($columns);

        $this->expectException(InvalidQueryOrderException::class);

        $queryParser->parseQuery(['order' => $orderings], false, true);
    }

    /**
     * @dataProvider getFilters
     */
    public function testFilters(array $columns, array $filters, Criteria $expected): void
    {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns($columns);

        $actual = $queryParser->parseQuery($filters);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getWrongFilters
     */
    public function testFiltersFail(array $columns, array $filters): void
    {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns($columns);

        $this->expectException(InvalidQueryExpressionException::class);

        $queryParser->parseQuery($filters, true);
    }
}
