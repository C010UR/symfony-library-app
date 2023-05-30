import { popup } from '~/components/tags/index.js';
import Axios from 'axios';
import { setupCache } from 'axios-cache-interceptor';

const axios = setupCache(Axios, {
  debug: console.log,
});

export async function useFetch({
  url,
  method = 'GET',
  params = {},
  data = {},
  contentType = 'json',
  suppressPopup = false,
  cache = true,
}) {
  method = method.toUpperCase().trim();
  contentType = contentType.toLowerCase().trim();

  switch (contentType) {
    case 'form-data': {
      contentType = 'multipart/form-data';
    }
    default: {
      contentType = 'application/json';
    }
  }

  try {
    const res = await axios.request({
      url: url,
      method: method,
      withCredentials: true,
      headers: {
        'Content-Type': contentType,
      },
      params: params,
      data,
      responseType: 'json',
      cache,
    });

    return res;
  } catch (error) {
    if (!suppressPopup) {
      if (error.response) {
        popup(
          'error',
          `<strong>HTTP ${error.response.status}</strong>: ${error.response.data.exception.message}`,
        );
      } else {
        popup('error', error);
      }
    }

    return null;
  }
}

function getFormData(object) {
  const formData = new FormData();

  for (const [key, value] of Object.entries(object)) {
    if (value !== null && Array.isArray(value)) {
      formData.append(key, new Blob(value));
      // for (const row of value) {
      //   formData.append(`${key}[]`, row);
      // }
    } else if (value !== null) {
      formData.append(key, value);
    }
  }

  return formData;
}
