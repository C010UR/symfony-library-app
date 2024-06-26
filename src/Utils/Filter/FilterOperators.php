<?php

namespace App\Utils\Filter;

/**
 * Class encapsulates operators for QueryParser.
 *
 * @see \App\Tests\Utils\Filter\FilterOperatorsTest
 */
class FilterOperators
{
    // Available operators
    /**
     * @var string
     */
    final public const OPERATOR_EQUALS_TO = 'eq';

    /**
     * @var string
     */
    final public const OPERATOR_GREATER_THAN = 'gt';

    /**
     * @var string
     */
    final public const OPERATOR_LESS_THAN = 'lt';

    /**
     * @var string
     */
    final public const OPERATOR_GREATER_OR_EQUAL_TO = 'gte';

    /**
     * @var string
     */
    final public const OPERATOR_LESS_OR_EQUAL_TO = 'lte';

    /**
     * @var string
     */
    final public const OPERATOR_NOT_EQUALS_TO = 'neq';

    /**
     * @var string
     */
    final public const OPERATOR_IS_NULL = 'null';

    /**
     * @var string
     */
    final public const OPERATOR_IN = 'in';

    /**
     * @var string
     */
    final public const OPERATOR_NOT_IN = 'not-in';

    /**
     * @var string
     */
    final public const OPERATOR_CONTAINS = 'contains';

    /**
     * @var string
     */
    final public const OPERATOR_STARTS_WITH = 'starts-with';

    /**
     * @var string
     */
    final public const OPERATOR_ENDS_WITH = 'ends-with';

    /**
     * @var string
     */
    final public const OPERATOR_BETWEEN = 'between';

    /**
     * Get all available operators.
     */
    public static function getAll(): array
    {
        $class = new \ReflectionClass(self::class);

        return self::format($class->getConstants());
    }

    /**
     * Get operators for number types.
     */
    public static function getForNumberTypes(bool $isNullable = false): array
    {
        $operators = self::format([
            self::OPERATOR_EQUALS_TO,
            self::OPERATOR_NOT_EQUALS_TO,
            self::OPERATOR_GREATER_THAN,
            self::OPERATOR_LESS_THAN,
            self::OPERATOR_GREATER_OR_EQUAL_TO,
            self::OPERATOR_LESS_OR_EQUAL_TO,
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
            self::OPERATOR_BETWEEN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Get operators for string type.
     */
    public static function getForStringTypes(bool $isNullable = false): array
    {
        $operators = self::format([
            self::OPERATOR_EQUALS_TO,
            self::OPERATOR_NOT_EQUALS_TO,
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
            self::OPERATOR_CONTAINS,
            self::OPERATOR_STARTS_WITH,
            self::OPERATOR_ENDS_WITH,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Get operators for date type.
     */
    public static function getForDateTypes(bool $isNullable = false): array
    {
        $operators = self::format([
            self::OPERATOR_EQUALS_TO,
            self::OPERATOR_NOT_EQUALS_TO,
            self::OPERATOR_GREATER_OR_EQUAL_TO,
            self::OPERATOR_LESS_OR_EQUAL_TO,
            self::OPERATOR_BETWEEN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Get operators for boolean type.
     */
    public static function getForBoolTypes(bool $isNullable = false): array
    {
        $operators = self::format([self::OPERATOR_EQUALS_TO]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Get operators for single entity type.
     */
    public static function getForEntityTypes(bool $isNullable = false): array
    {
        $operators = self::format([
            self::OPERATOR_EQUALS_TO,
            self::OPERATOR_NOT_EQUALS_TO,
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Get operators for multiple entities type.
     */
    public static function getForEntitiesTypes(bool $isNullable = false): array
    {
        $operators = self::format([
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Get operators for array types.
     */
    public static function getForArrayTypes(bool $isNullable = false): array
    {
        $operators = self::format([
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
            self::OPERATOR_CONTAINS,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    /**
     * Add null operators.
     */
    private static function withNullOperators(array $operators): array
    {
        return array_merge($operators, self::format([self::OPERATOR_IS_NULL]));
    }

    /**
     * Format operators.
     */
    public static function format(array $operators): array
    {
        $labels = [
            self::OPERATOR_EQUALS_TO => '=',
            self::OPERATOR_NOT_EQUALS_TO => '<>',
            self::OPERATOR_GREATER_THAN => '>',
            self::OPERATOR_LESS_THAN => '<',
            self::OPERATOR_GREATER_OR_EQUAL_TO => '>=',
            self::OPERATOR_LESS_OR_EQUAL_TO => '<=',
            self::OPERATOR_IS_NULL => 'Empty',
            self::OPERATOR_IN => 'Includes',
            self::OPERATOR_NOT_IN => 'Does not include',
            self::OPERATOR_CONTAINS => 'Contains',
            self::OPERATOR_STARTS_WITH => 'Starts With',
            self::OPERATOR_ENDS_WITH => 'Ends With',
            self::OPERATOR_BETWEEN => 'Between',
        ];

        $result = [];

        foreach ($operators as $operator) {
            $result[$operator] = [
                'operator' => $operator,
                'label' => $labels[$operator],
            ];
        }

        return $result;
    }
}
