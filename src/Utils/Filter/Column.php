<?php

namespace App\Utils\Filter;

class Column
{
    public const BOOLEAN_TYPE = 'boolean';
    public const INTEGER_TYPE = 'integer';
    public const FLOAT_TYPE = 'float';
    public const STRING_TYPE = 'string';
    public const ENTITY_TYPE = 'entity';
    public const DATE_TYPE = 'date';
    public const NOT_FILTERABLE_TYPE = 'none';

    private array $operators = [];
    private array $data = [];
    private string $name = '';
    private string $label = '';
    private string $type = '';
    private bool $isOrderable = false;

    public function __construct(
        string $name,
        string $label,
        string $type,
        bool $isOrderable,
        bool $isNullable = false,
        array $data = [],
    ) {
        $this->setName($name)
            ->setLabel($label)
            ->setType($type, $isNullable)
            ->setIsOrderable($isOrderable)
            ->setData($data);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setType(string $type, bool $isNullable = false): self
    {
        switch ($type) {
            case self::BOOLEAN_TYPE:
                $this->operators = FilterOperators::getForBoolTypes($isNullable);
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
            case self::DATE_TYPE:
                $this->operators = FilterOperators::getForDateTypes($isNullable);
                break;
            case self::NOT_FILTERABLE_TYPE:
                break;
            default:
                throw new \InvalidArgumentException(sprintf("Data type '%s' is not implemented.", $type));
        }

        $this->type = $type;

        return $this;
    }

    public function setIsOrderable(bool $isOrderable): self
    {
        $this->isOrderable = $isOrderable;

        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

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

    public function getData(): array
    {
        return $this->data;
    }

    public function getAll(): array
    {
        $formatted = [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'operators' => $this->getOperators(),
            'is_orderable' => $this->isOrderable(),
        ];

        if (!empty($this->getData())) {
            $formatted['data'] = $this->getData();
        }

        return $formatted;
    }

    public function isValidOperator(string $operator): bool
    {
        return in_array(strtolower($operator), $this->getOperators());
    }

    public static function convert(Column $column, string $data, bool $isArray): mixed
    {
        if ($isArray) {
            $data = explode(',', $data);
            $result = [];
            foreach ($data as $dataPoint) {
                $result[] = self::convertDataPoint($column->getType(), $dataPoint);
            }

            return $result;
        } else {
            return self::convertDataPoint($column->getType(), $data);
        }
    }

    private static function convertDataPoint(string $type, string $data): mixed
    {
        switch ($type) {
            case self::BOOLEAN_TYPE:
                return boolval($data);
            case self::INTEGER_TYPE:
            case self::ENTITY_TYPE:
                return intval($data);
            case self::FLOAT_TYPE:
                return floatval($data);
            case self::DATE_TYPE:
                return new \DateTimeImmutable($data);
            default:
                return $data;
        }
    }
}
