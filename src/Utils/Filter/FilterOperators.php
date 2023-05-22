<?php

namespace App\Utils\Filter;

use ReflectionClass;

class FilterOperators
{
    public const OPERATOR_EQUALS_TO = 'eq';
    public const OPERATOR_GREATER_THAN = 'gt';
    public const OPERATOR_LESS_THAN = 'lt';
    public const OPERATOR_GREATER_OR_EQUAL_TO = 'gte';
    public const OPERATOR_LESS_OR_EQUAL_TO = 'lte';
    public const OPERATOR_NOT_EQUALS_TO = 'neq';
    public const OPERATOR_IS_NULL = 'null';
    public const OPERATOR_IS_NOT_NULL = 'not-null';
    public const OPERATOR_IN = 'in';
    public const OPERATOR_NOT_IN = 'not-in';
    public const OPERATOR_CONTAINS = 'contains';
    public const OPERATOR_STARTS_WITH = 'starts-with';
    public const OPERATOR_ENDS_WITH = 'ends-with';
    public const OPERATOR_BETWEEN = 'between';

    public static function getAll(): array
    {
        $class = new ReflectionClass(self::class);
        return self::labelOperators($class->getConstants());
    }

    public static function getForNumberTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([
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

    public static function getForStringTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([
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

    public static function getForDateTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([
            self::OPERATOR_EQUALS_TO,
            self::OPERATOR_NOT_EQUALS_TO,
            self::OPERATOR_GREATER_OR_EQUAL_TO,
            self::OPERATOR_LESS_OR_EQUAL_TO,
            self::OPERATOR_BETWEEN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    public static function getForBoolTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([self::OPERATOR_EQUALS_TO, self::OPERATOR_NOT_EQUALS_TO]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    public static function getForEntityTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([
            self::OPERATOR_EQUALS_TO,
            self::OPERATOR_NOT_EQUALS_TO,
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    private static function withNullOperators(array $operators): array
    {
        return array_merge($operators, self::labelOperators([self::OPERATOR_IS_NOT_NULL, self::OPERATOR_IS_NULL]));
    }

    private static function labelOperators(array $operators): array
    {
        $labels = [];
        $class = new ReflectionClass(self::class);
        foreach ($class->getConstants() as $name => $value) {
            if (substr($name, 0, 8) !== 'OPERATOR') {
                continue;
            }

            $name = substr($name, 9);
            $name = str_replace('_', ' ', $name);
            $name = ucwords(strtolower($name));

            $labels[$value] = $name;
        }

        $result = [];

        foreach ($operators as $operator) {
            $result[$labels[$operator]] = $operator;
        }

        return $result;
    }
}
