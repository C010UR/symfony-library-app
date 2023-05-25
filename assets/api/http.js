import { unref } from 'vue';
import { popup } from '~/components/tags/index.js';
import { useCacheResponseStore } from '~/stores/cacheResponseStore';

async function request({
  url,
  options = {},
  parameters = {},
  isJson = false,
  suppressPopup = false,
  key,
}) {
  const cache = useCacheResponseStore();

  if (key !== undefined && cache.getCachedResponse(unref(key))) {
    console.log(`Cached: ${key}`);
    return cache.getCachedResponse(unref(key));
  }

  options.credentials = 'include';

  let parametersString = '';
  if (Object.keys(parameters).length > 0) {
    parametersString = '?' + new URLSearchParams(parameters);
  }

  options.headers = new Headers(options.headers);

  if (isJson) {
    options.headers.set('content-type', 'application/json');
  }

  let response = null;

  try {
    response = await fetch(`${url}${parametersString}`, options);
  } catch {}

  const data = parseResponse(response, suppressPopup);

  if (key !== undefined) {
    cache.addCachedResponse(unref(key), data);
  }

  return data;
}

async function parseResponse(response, suppressPopup = false) {
  const _popup = suppressPopup ? () => {} : popup;

  if (!response) {
    _popup('error', 'Невозможно подключиться к серверу!');

    return null;
  }

  const data = response.status === 204 ? {} : await response.json();

  // if (response.status == 401 || response.status == 403) {
  //   cache.$reset();
  // }

  if (!response.ok) {
    _popup(
      'error',
      `<strong>HTTP-Ошибка ${response.status}:</strong> ${data.exception.message}`,
    );

    return null;
  }

  return data;
}

function createFormData(data) {
  const formData = new FormData();

  for (const [key, value] of Object.entries(data)) {
    if (value !== null && Array.isArray(value)) {
      for (const row of value) {
        formData.append(`${key}[]`, row);
      }
    } else if (value !== null) {
      formData.append(key, value);
    }
  }

  return formData;
}

export { request, createFormData };
