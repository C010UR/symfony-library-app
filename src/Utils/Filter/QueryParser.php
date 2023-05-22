<?php

namespace App\Utils\Filter;

use App\Utils\Filter\Exception\InvalidQueryExpressionException;
use App\Utils\Filter\Exception\InvalidQueryOrderException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use InvalidArgumentException;
use Throwable;

class QueryParser
{
    private array $columns;
    private Criteria $criteria;

    public function __construct()
    {
        $this->criteria = Criteria::create();
    }

    public function setAllowedColumns(Column|array $columns): self
    {
        if (!is_array($columns)) {
            $this->columns = [$columns];

            return $this;
        }

        foreach ($columns as $column) {
            if (gettype($column) !== 'object' || $column::class !== Column::class) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Columns should be of type %s. %s was provided instead.',
                        Column::class,
                        gettype($column) === 'object' ? $column::class : gettype($column),
                    ),
                );
            }
        }

        $this->columns = $columns;

        return $this;
    }

    public function getAllowedColumns(): array
    {
        $result = [];

        foreach ($this->columns as $column) {
            $result[] = $column->getAll();
        }

        return $result;
    }

    private function findColumn(string $name): ?Column
    {
        $result = null;

        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }

        return $result;
    }

    private function setPaginatorOptions(?int $pageSize = null, ?int $offset = null): self
    {
        if ($pageSize !== null && $pageSize > 0) {
            $this->criteria->setMaxResults($pageSize);
        }

        if ($offset !== null && $offset >= 0) {
            $this->criteria->setMaxResults($pageSize)->setFirstResult($offset);
        }

        return $this;
    }

    private function setOrderings(array $orderings): self
    {
        foreach ($orderings as $columnName => $order) {
            $column = $this->findColumn($columnName);

            if ($column === null) {
                throw new InvalidQueryOrderException(sprintf("Column '%s' is not valid.", $columnName));
            }

            if (!$column->isOrderable()) {
                throw new InvalidQueryOrderException(sprintf("Column '%s' is not orderable.", $columnName));
            }

            $order = strtoupper($order);

            if ($order !== Criteria::ASC && $order !== Criteria::DESC) {
                throw new InvalidQueryOrderException(
                    sprintf(
                        "Order '%s' for column '%s' is not valid. Acceptable orders are ASC and DESC.",
                        $order,
                        $columnName,
                    ),
                );
            }
        }

        $this->criteria->OrderBy($orderings);

        return $this;
    }

    private function parseOperator(string $column, string $operator, mixed $value): Expression
    {
        switch ($operator) {
            case FilterOperators::OPERATOR_IS_NULL:
                return Criteria::expr()->isNull($column);
            case FilterOperators::OPERATOR_IS_NOT_NULL:
                return Criteria::expr()->neq($column, 'NULL');
            case FilterOperators::OPERATOR_EQUALS_TO:
                return Criteria::expr()->eq($column, $value);
            case FilterOperators::OPERATOR_NOT_EQUALS_TO:
                return Criteria::expr()->neq($column, $value);
            case FilterOperators::OPERATOR_GREATER_THAN:
                return Criteria::expr()->gt($column, $value);
            case FilterOperators::OPERATOR_GREATER_OR_EQUAL_TO:
                return Criteria::expr()->gte($column, $value);
            case FilterOperators::OPERATOR_LESS_THAN:
                return Criteria::expr()->lt($column, $value);
            case FilterOperators::OPERATOR_LESS_OR_EQUAL_TO:
                return Criteria::expr()->lte($column, $value);
            case FilterOperators::OPERATOR_IN:
                return Criteria::expr()->in($column, $value);
            case FilterOperators::OPERATOR_NOT_IN:
                return Criteria::expr()->notIn($column, $value);
            case FilterOperators::OPERATOR_CONTAINS:
                return Criteria::expr()->contains($column, $value);
            case FilterOperators::OPERATOR_BETWEEN:
                if (count($value) !== 2) {
                    throw new InvalidQueryExpressionException(
                        sprintf(
                            'Operator between can only accept 2 values. (Column: %s)',
                            $column,
                        )

                    );
                }
                return Criteria::expr()->andX(
                    Criteria::expr()->gte($column, $value[0]),
                    Criteria::expr()->lte($column, $value[1]),
                );
            case FilterOperators::OPERATOR_STARTS_WITH:
                return Criteria::expr()->startsWith($column, $value);
            case FilterOperators::OPERATOR_ENDS_WITH:
                return Criteria::expr()->endsWith($column, $value);
            default:
                throw new InvalidArgumentException(sprintf("Operator '%s' is not implemented.", $operator));
        }
    }

    private function setExpression(string $columnName, string $operator, string $value): self
    {
        $column = $this->findColumn($columnName);

        if ($column === null) {
            throw new InvalidQueryExpressionException(sprintf("Column '%s' is not valid.", $columnName));
        }

        if (!$column->isValidOperator($operator)) {
            throw new InvalidQueryExpressionException(
                sprintf(
                    "Operator '%s' for column '%s' is not valid. Acceptable values are: %s",
                    $operator,
                    $columnName,
                    implode(', ', $column->getOperators()),
                ),
            );
        }

        $isArray =
            FilterOperators::OPERATOR_IN === $operator ||
            FilterOperators::OPERATOR_NOT_IN === $operator ||
            FilterOperators::OPERATOR_BETWEEN === $operator;

        try {
            $value = Column::convert($column, $value, $isArray);
        } catch (Throwable $th) {
            throw new InvalidQueryExpressionException(
                message: sprintf(
                    "Invalid value in '%s' %s '%s'. Reason: %s",
                    $column->getLabel(),
                    $operator,
                    $value,
                    $th->getMessage(),
                ),
                previous: $th,
            );
        }

        try {
            $expr = $this->parseOperator($column->getName(), $operator, $value);
        } catch (Throwable $th) {
            throw new InvalidQueryExpressionException(
                message: sprintf(
                    "Invalid value in '%s' %s '%s'. Reason: %s",
                    $column->getName(),
                    $operator,
                    $value,
                    $th->getMessage(),
                ),
                previous: $th,
            );
        }

        $this->criteria->andWhere($expr);

        return $this;
    }

    private function getCriteria(): Criteria
    {
        $criteria = $this->criteria;
        $this->criteria = Criteria::create();

        return $criteria;
    }

    /**
     * @throws InvalidQueryOrderException
     * @throws InvalidQueryExpressionException
     */
    public function parseQuery(array $query, bool $parsePages = false, bool $parseOrder = false): Criteria
    {
        if ($parsePages) {
            $this->setPaginatorOptions($query['page_size'] ?? null, $query['offset'] ?? null);
        }

        unset($query['page_size']);
        unset($query['offset']);

        if ($parseOrder && !empty($query['order'])) {
            $this->setOrderings($query['order']);
        }

        unset($query['order']);

        foreach ($query as $name => $options) {
            if (!isset($options) || !is_array($options)) {
                continue;
            }

            foreach ($options as $operator => $value) {
                $this->setExpression($name, $operator, $value);
            }
        }

        return $this->getCriteria();
    }
}