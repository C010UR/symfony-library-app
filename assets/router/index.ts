import { nextTick } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { ElLoading } from 'element-plus';
import { resolveTransition } from './transition-resolver';
import { isUserHasPermissions, routeFallback } from './permissions-resolver';
import {
  LoginView,
  RequestPasswordResetView,
  RequestPasswordResetConfirmView,
  ResetPasswordView,
  NotFoundView,
} from '@/views';
import { useGetProfile } from '@/use';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Main',
      component: NotFoundView,
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: NotFoundView,
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
      component: RequestPasswordResetView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 2,
      },
    },
    {
      path: '/password-reset/:pathMatch(.{40})',
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
      component: RequestPasswordResetConfirmView,
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

  if (to.name === 'Dashboard') {
    return routeFallback(await useGetProfile());
  }

  if (!to.meta.roles) {
    return undefined;
  }

  const profile = await useGetProfile();

  if (!isUserHasPermissions(profile, to.meta.roles as UserRole[] | undefined)) {
    return routeFallback(profile);
  }

  // if (to.meta.store) {
  //   const crudStore = useCrudStore(to.meta.store);
  //   crudStore.parseQuery(to.query);
  // }
});

router.afterEach(async to => {
  // Disable fullscreen loading if it is present
  const loadingInstance = ElLoading.service({ fullscreen: true });

  nextTick(() => {
    loadingInstance.close();
    document.title = to.meta.title ? to.meta.title + ' :: МТЭК' : 'МТЭК';
  });
});

export default router;