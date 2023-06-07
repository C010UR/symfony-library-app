import type { ApiParams, Order, Filter, Pagination, FilterOperator } from './types';

export function useParseApiParams(params?: ApiParams) {
  if (!params) {
    return undefined;
  }

  const result: { orders?: Order[]; filters?: Filter[]; pagination: Pagination; deleted?: boolean } = {
    pagination: {
      pageSize: params.pageSize && Number(params.pageSize) >= 0 ? Number(params.pageSize) : 20,
      offset: params.offset && Number(params.offset) >= 0 ? Number(params.offset) : 0,
    },
  };

  if (params.order !== undefined) {
    result.orders = [];

    for (const [column, direction] of Object.entries(params.order)) {
      result.orders.push({
        column,
        direction,
      });
    }
  }

  result.deleted = params.deleted ? true : undefined;

  delete params.order;
  delete params.deleted;
  delete params.offset;
  delete params.pageSize;

  if (Object.keys(params).length === 0) {
    return result;
  }

  result.filters = [];

  for (const [column, filters] of Object.entries(params)) {
    if (typeof filters !== 'object') {
      continue;
    }

    for (const [operator, value] of Object.entries(filters as object)) {
      if (typeof value !== 'string') {
        continue;
      }

      const _value = value.split(',');

      result.filters.push({
        column,
        operator: operator as FilterOperator,
        value: _value.length === 1 ? _value[0] : _value,
      });
    }
  }

  return result;
}

function useParseOrders(orders?: Order[]): ApiParams | undefined {
  if (orders === undefined) {
    return undefined;
  }

  const result: ApiParams = {};

  if (result.order === undefined) {
    result.order = {};
  }

  for (const order of orders) {
    result.order[order.column] = order.direction;
  }

  return result;
}

function useParseFilters(filters?: Filter[]): ApiParams | undefined {
  if (filters === undefined) {
    return undefined;
  }

  const result: ApiParams = {};

  for (const filter of filters) {
    let value: string;

    if (Array.isArray(filter.value)) {
      if (filter.value[0] instanceof Date) {
        value = (filter.value as Date[]).map(value => value.toISOString().split('T')[0]).join(',');
      } else {
        value = filter.value.join(',');
      }
    } else if (filter.value instanceof Date) {
      value = filter.value.toISOString().split('T')[0];
    } else {
      value = String(filter.value);
    }

    if (result[filter.column] === undefined) {
      result[filter.column] = {};
    }

    if (filter.operator !== undefined) {
      (
        result[filter.column] as {
          [operator in FilterOperator]?: string;
        }
      )[filter.operator] = value;
    }
  }

  return result;
}

function useParsePagination(pagination?: Pagination): ApiParams | undefined {
  if (pagination === undefined) {
    return undefined;
  }

  return {
    offset: pagination.offset,
    pageSize: pagination.pageSize,
  };
}

export function useParseParams(filters?: Filter[], orders?: Order[], pagination?: Pagination): ApiParams | undefined {
  if (filters === undefined && orders === undefined && pagination === undefined) {
    return undefined;
  }

  return {
    ...useParseFilters(filters),
    ...useParseOrders(orders),
    ...useParsePagination(pagination),
  };
}
