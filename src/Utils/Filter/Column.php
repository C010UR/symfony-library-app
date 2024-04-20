<?php

namespace App\Utils\Filter;

/**
 * Class encapsulates filtration/order/search options for QueryParser.
 *
 * @see \App\Tests\Utils\Filter\ColumnTest
 */
class Column
{
    // Available column types
    /**
     * @var string
     */
    final public const BOOLEAN_TYPE = 'boolean';

    /**
     * @var string
     */
    final public const INTEGER_TYPE = 'integer';

    /**
     * @var string
     */
    final public const FLOAT_TYPE = 'float';

    /**
     * @var string
     */
    final public const STRING_TYPE = 'string';

    /**
     * @var string
     */
    final public const ENTITY_TYPE = 'entity';

    /**
     * @var string
     */
    final public const ENTITIES_TYPE = 'entities';

    /**
     * @var string
     */
    final public const DATE_TYPE = 'date';

    /**
     * @var string
     */
    final public const ARRAY_TYPE = 'array';

    /**
     * @var string
     */
    final public const NOT_FILTERABLE_TYPE = 'not_filterable';

    private array $operators = [];

    private array $data = [];

    private string $name = '';

    private string $label = '';

    private string $type = '';

    private bool $isOrderable = false;

    private bool $isSearchable = false;

    public function __construct(
        array $data
    ) {
        if (!array_key_exists('name', $data) || $data['name'] instanceof string || !$data['name']) {
            throw new \InvalidArgumentException('Column name is not provided or does not have a valid value.');
        }

        if (!array_key_exists('type', $data) || $data['type'] instanceof string || !$data['type']) {
            throw new \InvalidArgumentException(sprintf("Column type for column '%s' is not provided or does not have a valid value.", $data['name']));
        }

        if (
            self::NOT_FILTERABLE_TYPE !== $data['type']
            && (!array_key_exists('label', $data)
                || $data['label'] instanceof string
                || !$data['label']
            )
        ) {
            throw new \InvalidArgumentException(sprintf("Column label for column '%s' is not provided or does not have a valid value.", $data['name']));
        }

        if ((self::ENTITY_TYPE == $data['type'] || self::ENTITIES_TYPE == $data['type']) && !array_key_exists('entity', $data)) {
            throw new \InvalidArgumentException(sprintf("Entity not specified for column '%s'", $data['name']));
        }

        $this->setName($data['name']);

        if (array_key_exists('label', $data)) {
            $this->setLabel($data['label']);
        }

        $this->setType(
            $data['type'],
            array_key_exists('isNullable', $data) ? $data['isNullable'] : false,
            $data['entity'] ?? null
        );

        $this->setIsOrderable(array_key_exists('isOrderable', $data) ? $data['isOrderable'] : false);
        $this->setIsSearchable(array_key_exists('isSearchable', $data) ? $data['isSearchable'] : false);
    }

    /**
     * Set the name of the column.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the label of the column. Display it for the user instead of the name.
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set type of the column. Operators on columns depend on it.
     */
    public function setType(string $type, bool $isNullable = false, string $entity = null): self
    {
        switch ($type) {
            case self::BOOLEAN_TYPE:
                $this->operators = FilterOperators::getForBoolTypes($isNullable);
                $this->data['bool'] = [
                    'true' => 'Да',
                    'false' => 'Нет',
                ];
                break;
            case self::INTEGER_TYPE:
            case self::FLOAT_TYPE:
                $this->operators = FilterOperators::getForNumberTypes($isNullable);
                break;
            case self::STRING_TYPE:
                $this->operators = FilterOperators::getForStringTypes($isNullable);
                break;
            case self::ENTITY_TYPE:
                $this->operators = FilterOperators::getForEntityTypes($isNullable);
                break;
            case self::ENTITIES_TYPE:
                $this->operators = FilterOperators::getForEntitiesTypes($isNullable);
                break;
            case self::DATE_TYPE:
                $this->operators = FilterOperators::getForDateTypes($isNullable);
                break;
            case self::NOT_FILTERABLE_TYPE:
                break;
            case self::ARRAY_TYPE:
                $this->operators = FilterOperators::getForArrayTypes($isNullable);
                break;
            default:
                throw new \InvalidArgumentException(sprintf("Data type '%s' is not implemented.", $type));
        }

        $this->type = $type;

        if ($isNullable) {
            $this->data['null'] = [
                'true' => 'Yes',
                'false' => 'No',
            ];
        }

        if (null !== $entity && '' !== $entity) {
            $this->data['entity'] = $entity;
        }

        return $this;
    }

    /**
     * Set if result can be ordered by this column.
     */
    public function setIsOrderable(bool $isOrderable): self
    {
        $this->isOrderable = $isOrderable;

        return $this;
    }

    /**
     * Set if this column is included in list of search columns.
     */
    public function setIsSearchable(bool $isSearchable): self
    {
        $this->isSearchable = $isSearchable;

        return $this;
    }

    /**
     * Set additional column data.
     */
    public function addData(mixed $data): self
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * Get the name of a column.
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isOrderable(): bool
    {
        return $this->isOrderable;
    }

    public function getOperators(): array
    {
        return $this->operators;
    }

    public function isEntity(): bool
    {
        return array_key_exists('entity', $this->getData());
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    /**
     * Get formatted information about the column.
     */
    public function getAll(): array
    {
        $formatted = [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'operators' => $this->getOperators(),
            'isOrderable' => $this->isOrderable(),
            'isSearchable' => $this->isSearchable(),
        ];

        if ([] !== $this->getData()) {
            $formatted['data'] = $this->getData();
        }

        return $formatted;
    }

    /**
     * Check if operator can be used on this column.
     */
    public function isValidOperator(string $operator): bool
    {
        return array_key_exists(strtolower($operator), $this->getOperators());
    }

    /**
     * Convert query string data to appropriate type.
     */
    public static function convert(Column $column, string $data, bool $isArray): mixed
    {
        if ($isArray) {
            $data = explode(',', $data);
            $result = [];
            foreach ($data as $dataPoint) {
                $result[] = self::convertDataPoint($column->getType(), $dataPoint);
            }

            return $result;
        }

        return self::convertDataPoint($column->getType(), $data);
    }

    /**
     * Convert query string data to appropriate type.
     */
    private static function convertDataPoint(string $type, string $data): mixed
    {
        return match ($type) {
            self::BOOLEAN_TYPE => (bool) $data,
            self::INTEGER_TYPE, self::ENTITY_TYPE, self::ENTITIES_TYPE => (int) $data,
            self::FLOAT_TYPE => (float) $data,
            self::DATE_TYPE => new \DateTime($data),
            default => $data,
        };
    }
}
