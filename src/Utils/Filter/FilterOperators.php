<?php

namespace App\Utils\Filter;

class FilterOperators
{
    public const OPERATOR_EQUALS_TO = 'eq';
    public const OPERATOR_GREATER_THAN = 'gt';
    public const OPERATOR_LESS_THAN = 'lt';
    public const OPERATOR_GREATER_OR_EQUAL_TO = 'gte';
    public const OPERATOR_LESS_OR_EQUAL_TO = 'lte';
    public const OPERATOR_NOT_EQUALS_TO = 'neq';
    public const OPERATOR_IS_NULL = 'null';
    public const OPERATOR_IN = 'in';
    public const OPERATOR_NOT_IN = 'not-in';
    public const OPERATOR_CONTAINS = 'contains';
    public const OPERATOR_STARTS_WITH = 'starts-with';
    public const OPERATOR_ENDS_WITH = 'ends-with';
    public const OPERATOR_BETWEEN = 'between';

    public static function getAll(): array
    {
        $class = new \ReflectionClass(self::class);

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
        $operators = self::labelOperators([self::OPERATOR_EQUALS_TO]);

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

    public static function getForEntitiesTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    public static function getForArrayTypes(bool $isNullable = false): array
    {
        $operators = self::labelOperators([
            self::OPERATOR_IN,
            self::OPERATOR_NOT_IN,
            self::OPERATOR_CONTAINS,
        ]);

        return $isNullable ? self::withNullOperators($operators) : $operators;
    }

    private static function withNullOperators(array $operators): array
    {
        return array_merge($operators, self::labelOperators([self::OPERATOR_IS_NULL]));
    }

    private static function labelOperators(array $operators): array
    {
        $labels = [
            self::OPERATOR_EQUALS_TO => 'Равняется',
            self::OPERATOR_GREATER_THAN => 'Больше',
            self::OPERATOR_LESS_THAN => 'Меньше',
            self::OPERATOR_GREATER_OR_EQUAL_TO => 'Больше либо равно',
            self::OPERATOR_LESS_OR_EQUAL_TO => 'Меньше либо равно',
            self::OPERATOR_NOT_EQUALS_TO => 'Не равно',
            self::OPERATOR_IS_NULL => 'Пустое',
            self::OPERATOR_IN => 'Включает',
            self::OPERATOR_NOT_IN => 'Не Включает',
            self::OPERATOR_CONTAINS => 'Содержит',
            self::OPERATOR_STARTS_WITH => 'Начинается на',
            self::OPERATOR_ENDS_WITH => 'Заканчивается на',
            self::OPERATOR_BETWEEN => 'Между',
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
