type FilterOperator =
  | 'eq'
  | 'gt'
  | 'lt'
  | 'gte'
  | 'lte'
  | 'neq'
  | 'null'
  | 'in'
  | 'not-in'
  | 'contains'
  | 'starts-with'
  | 'ends-with'
  | 'between';
type FilterType = 'boolean' | 'integer' | 'float' | 'string' | 'entity' | 'entities' | 'date' | 'array' | 'none';
type FilterEntityType = 'book' | 'author' | 'tag' | 'publisher';
type OrderDirection = 'ASC' | 'DESC';

interface Order {
  column: string;
  direction: OrderDirection;
}

interface Filter {
  column: string;
  operator?: FilterOperator;
  value?: unknown | [unknown, unknown];
}

interface FilterParams {
  order?: {
    [columnName: string]: OrderDirection;
  };
  [column: string]: {
    [operator in FilterOperator]?: string;
  };
}

interface BaseFilterOption {
  name: string;
  label: string;
  operators: Record<
    FilterOperator,
    {
      operator: FilterOperator;
      label: string;
    }
  >;
  isOrderable: boolean;
  data?: {
    null?: {
      true: string;
      false: string;
    };
  };
}

interface NormalFilterOption extends BaseFilterOption {
  type: 'integer' | 'float' | 'string' | 'date' | 'array' | 'none';
}

interface BooleanFilterOption extends BaseFilterOption {
  type: 'boolean';
  data: {
    bool: {
      true: string;
      false: string;
    };
    null?: {
      true: string;
      false: string;
    };
  };
}

interface EntityFilterOption extends BaseFilterOption {
  type: 'entity' | 'entities';
  data: {
    entity: FilterEntityType;
    null?: {
      true: string;
      false: string;
    };
  };
}

type FilterOption = NormalFilterOption | BooleanFilterOption | EntityFilterOption;
