import { request } from './http.js';

async function login(username, password) {
  return await request({
    url: '/api/v1/login',
    isJson: true,
    options: {
      method: 'POST',
      body: JSON.stringify({
        username,
        password,
      }),
    },
  });
}

async function logout() {
  return await request({ url: '/api/v1/login' });
}

async function getProfile() {
  return await request({
    url: '/api/v1/profile',
    key: 'profile',
    suppressPopup: true,
  });
}

async function requestResetPassword(email, link) {
  return await request({
    url: '/api/v1/password-reset',
    isJson: true,
    options: {
      method: 'POST',
      body: JSON.stringify({
        email,
        link,
      }),
    },
  });
}

async function resetPassword(token, password) {
  return await request({
    url: `/api/v1/password-reset/reset/${token}`,
    isJson: true,
    options: {
      method: 'POST',
      body: JSON.stringify({
        password,
      }),
    },
  });
}

export { login, logout, getProfile, resetPassword, requestResetPassword };
