import Axios from 'axios';
import { buildWebStorage, setupCache } from 'axios-cache-interceptor';

const axiosInstance = Axios.create({
  timeout: 60 * 1000, // 1 minute
});

export const axios = setupCache(axiosInstance, {
  storage: buildWebStorage(localStorage, 'axios-cache:'),
});
