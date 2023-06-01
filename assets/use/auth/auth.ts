import { useFetch } from './../useFetch';

async function useLogin(username: string, password: string) {
  return await useFetch<UserProfile>({
    url: '/api/v1/login',
    method: 'POST',
    contentType: 'json',
    data: {
      username,
      password,
    },
    isCache: false,
  });
}

async function useLogout() {
  return await useFetch({ url: '/api/v1/login', method: 'GET', contentType: 'json', isCache: false });
}

async function useGetProfile() {
  return await useFetch<UserProfile>({
    url: '/api/v1/profile',
    method: 'GET',
    contentType: 'json',
    isSuppressPopup: true,
  });
}

async function useRequestPasswordReset(email: string, link: string) {
  return await useFetch({
    url: '/api/v1/password-reset',
    method: 'POST',
    contentType: 'json',
    data: {
      email,
      link,
    },
    isCache: false,
  });
}

async function useResetPassword(token: string, password: string) {
  return await useFetch({
    url: `/api/v1/password-reset/reset/${token}`,
    method: 'POST',
    contentType: 'json',
    data: {
      password,
    },
    isCache: false,
  });
}

export { useLogin, useLogout, useGetProfile, useRequestPasswordReset, useResetPassword };
