import { useFetch } from '../useFetch';
import type { RouteParams } from '../useFetch';
import type { ApiCollection, FilterOption } from './types';

export const ApiUrls = {
  authors: '/api/v1/authors',
  publishers: '/api/v1/publishers',
  tags: '/api/v1/tags',
  books: '/api/v1/books',
  users: '/api/v1/users',
  orders: '/api/v1/orders',
} as const;

export type ApiUrl = (typeof ApiUrls)[keyof typeof ApiUrls];

export async function useGetAll<ReturnType>(
  url: ApiUrl | string,
  params?: RouteParams,
): Promise<ApiCollection<ReturnType> | undefined> {
  return await useFetch<ApiCollection<ReturnType>>({
    url,
    method: 'GET',
    contentType: 'json',
    params,
  });
}

export async function useGetOne<ReturnType>(url: ApiUrl | string, slug: string): Promise<ReturnType | undefined> {
  return await useFetch<ReturnType>({
    url: `${url}/${slug}`,
    method: 'GET',
    contentType: 'json',
  });
}

export async function useCreate<ReturnType, InputType>(
  url: ApiUrl | string,
  entity: InputType,
  isHasImage = false,
): Promise<ReturnType | undefined> {
  return await useFetch<ReturnType, InputType>({
    url,
    method: 'POST',
    contentType: isHasImage ? 'form-data' : 'json',
    data: entity,
  });
}

export async function useUpdate<ReturnType, InputType>(
  url: ApiUrl | string,
  id: number,
  entity: InputType,
  isHasImage = false,
): Promise<ReturnType | undefined> {
  return await useFetch<ReturnType, InputType>({
    url: `${url}/${id}`,
    method: 'POST',
    contentType: isHasImage ? 'form-data' : 'json',
    data: entity,
  });
}

export async function useDelete<T extends { id: number }>(url: ApiUrl | string, entity: T): Promise<boolean> {
  const result = await useFetch<T>({
    url: `${url}/${entity.id}`,
    method: 'DELETE',
    contentType: 'json',
  });

  return result !== undefined;
}

export async function useGetMeta(url: ApiUrl | string): Promise<FilterOption[] | undefined> {
  return await useFetch<FilterOption[]>({
    url: `${url}/meta`,
    method: 'GET',
    contentType: 'json',
  });
}
