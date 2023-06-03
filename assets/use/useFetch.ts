import { popup } from '@/components/tags';
import Axios from 'axios';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import type { AxiosError, Method } from 'axios';
import { setupCache } from 'axios-cache-interceptor';

const axios = setupCache(Axios, {
  debug: console.log,
});

export type { Method } from 'axios';

export type ContentType = 'json' | 'form-data';

export interface Params {
  [index: string]: string | string[] | number | number[] | object | object[];
}

export interface Data {
  [index: string]: unknown | unknown[];
}

export interface FetchParams<T> {
  url: string;
  method: Method;
  contentType: ContentType;
  params?: Params;
  data?: T;
  isSuppressPopup?: boolean;
  isCache?: false | undefined;
}

export async function useFetch<ReturnType, InputType = ReturnType>({
  url,
  method,
  params,
  data,
  contentType,
  isSuppressPopup = false,
  isCache = false,
}: FetchParams<InputType>): Promise<ReturnType | undefined> {
  let _contentType: string;

  console.log(`${method} ${url}`, params, data, contentType);

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
