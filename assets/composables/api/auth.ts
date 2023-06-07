import { useFetch } from '../useFetch';
import type { UserProfile } from './types';

export async function useLogin(username: string, password: string) {
  return await useFetch<UserProfile, { username: string; password: string }>({
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

export async function useLogout() {
  return await useFetch({ url: '/api/v1/logout', method: 'GET', contentType: 'json', isCache: false });
}

export async function useGetProfile() {
  return await useFetch<UserProfile>({
    url: '/api/v1/profile',
    method: 'GET',
    contentType: 'json',
    isSuppressPopup: true,
  });
}

export async function useRequestPasswordReset(email: string, link: string) {
  return await useFetch<string, { email: string; link: string }>({
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

export async function useResetPassword(token: string, password: string) {
  return await useFetch<string, { password: string }>({
    url: `/api/v1/password-reset/reset/${token}`,
    method: 'POST',
    contentType: 'json',
    data: {
      password,
    },
    isCache: false,
  });
}
