import { useFetch } from './../useFetch.js';

async function useLogin({ username, password }) {
  return await useFetch({
    url: '/api/v1/login',
    isJson: true,
    method: 'POST',
    data: {
      username,
      password,
    },
    cache: false,
  });
}

async function useLogout() {
  return await useFetch({ url: '/api/v1/login', cache: false });
}

async function useGetProfile() {
  return await useFetch({
    url: '/api/v1/profile',
    key: 'profile',
    suppressPopup: true,
  });
}

async function useRequestPasswordReset({ email, link }) {
  return await useFetch({
    url: '/api/v1/password-reset',
    method: 'POST',
    data: {
      email,
      link,
    },
    cache: false,
  });
}

async function useResetPassword({ token, password }) {
  return await useFetch({
    url: `/api/v1/password-reset/reset/${token}`,
    isJson: true,
    method: 'POST',
    data: {
      password,
    },
    cache: false,
  });
}

export {
  useLogin,
  useLogout,
  useGetProfile,
  useRequestPasswordReset,
  useResetPassword,
};
