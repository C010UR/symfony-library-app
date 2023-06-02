import { popup } from '@/components/tags';
import Axios from 'axios';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import type { AxiosError } from 'axios';
import { setupCache } from 'axios-cache-interceptor';

const axios = setupCache(Axios, {
  debug: console.log,
});

interface FetchParams {
  url: string;
  method: 'GET' | 'HEAD' | 'POST' | 'PUT' | 'DELETE' | 'CONNECT' | 'OPTIONS' | 'TRACE' | 'PATCH';
  contentType: 'json' | 'form-data';
  params?: {
    [index: string]: string | string[] | number | number[];
  };
  data?: {
    [index: string]: unknown | unknown[];
  };
  isSuppressPopup?: boolean;
  isCache?: false | undefined;
}

export async function useFetch<T>({
  url,
  method,
  params,
  data,
  contentType,
  isSuppressPopup = false,
  isCache = false,
}: FetchParams): Promise<T | null> {
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

    console.log(url, res);

    return (res.data as T) ?? null;
  } catch (error: AxiosError | unknown) {
    if (!isSuppressPopup) {
      if (Axios.isAxiosError(error) && error.response) {
        popup('error', error.response.data.exception.message, error.response.status);
      } else {
        popup('error', String(error));
      }
    }

    return null;
  }
}
