import { popup } from '@/components/tags';
import Axios from 'axios';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import type { AxiosError, Method } from 'axios';
import { axios } from '@/lib/axios';
import type { RouteLocationNormalizedLoaded, Router } from 'vue-router';
import type { RouteParams } from 'vue-router';

export type { Method } from 'axios';
export type { RouteParams } from 'vue-router';
export type ContentType = 'json' | 'form-data';

export interface FetchOptions<T> {
  url: string;
  method: Method;
  contentType: ContentType;
  params?: RouteParams;
  data?: T;
  isSuppressPopup?: boolean;
  isCache?: false;
}

export async function useFetch<ReturnType, InputType = ReturnType>({
  url,
  method,
  params,
  data,
  contentType,
  isSuppressPopup = false,
  isCache = false,
}: FetchOptions<InputType>): Promise<ReturnType | undefined> {
  let _contentType: string;

  switch (contentType) {
    case 'form-data': {
      _contentType = 'multipart/form-data';
      break;
    }
    case 'json': {
      _contentType = 'application/json';
      break;
    }
    default: {
      const _exhaustiveCheck: never = contentType;
      return _exhaustiveCheck;
    }
  }

  try {
    const res = await axios.request({
      url: url,
      method: method,
      withCredentials: true,
      headers: {
        'Content-Type': _contentType,
      },
      params: params,
      data,
      responseType: 'json',
      cache: isCache,
    });

    console.log(`${method} ${url} - ${contentType} - cached (${res.id}): ${res.cached}`, params, data);

    return res.data as ReturnType;
  } catch (error: AxiosError | unknown) {
    if (!isSuppressPopup) {
      if (Axios.isAxiosError(error) && error.response) {
        popup('error', error.response.data.exception.message, error.response.status);
      } else {
        popup('error', String(error));
      }
    }

    return undefined;
  }
}

export interface FetchOptionsWithParams<T> extends FetchOptions<T> {
  route: RouteLocationNormalizedLoaded;
  router: Router;
}
