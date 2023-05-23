import { nextTick } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { ElLoading } from 'element-plus';
import { resolveTransition } from './transition-resolver.js';
import { isAllowed, routeFallback } from './path-resolve.js';
import {
  LoginView,
  ResetPasswordRequestView,
  ResetPasswordView,
  ResetPasswordConfirmView,
  NotFoundView,
} from '~/views/index.js';
import { getProfile } from '~/api/index.js';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Main',
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginView,
      meta: {
        title: 'Авторизация',
        transitionType: 'Auth',
        transitionLevel: 1,
      },
    },
    {
      path: '/password-reset',
      name: 'ResetPasswordRequest',
      component: ResetPasswordRequestView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 2,
      },
    },
    {
      path: '/password-reset/:pathMatch(.*)',
      name: 'ResetPassword',
      component: ResetPasswordView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 5,
      },
    },
    {
      path: '/password-reset-confirm',
      name: 'ResetPasswordConfirm',
      component: ResetPasswordConfirmView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 3,
      },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: NotFoundView,
      meta: {
        title: 'Страница не найдена',
      },
    },
  ],
});

router.beforeEach(async (to, from) => {
  to.meta.transition = resolveTransition(to, from);

  if (!to.meta.roles) {
    return null;
  }

  const profile = await getProfile();

  if (to.name === 'Main') {
    return routeFallback(profile);
  }

  if (!isAllowed(profile, to.meta.roles)) {
    return routeFallback(profile);
  }

  // if (to.meta.store) {
  //   const crudStore = useCrudStore(to.meta.store);
  //   crudStore.parseQuery(to.query);
  // }
});

router.afterEach(async to => {
  // Disable loading if it is present
  const loadingInstance = ElLoading.service({ fullscreen: true });

  nextTick(() => {
    loadingInstance.close();
    document.title = to.meta.title ? to.meta.title + ' :: МТЭК' : 'МТЭК';
  });
});

export default router;
