import type { UserRole } from '@/composables';
import { useGetProfile } from '@/composables';
import {
  AboutUs,
  AuthorsCrudView,
  AuthorView,
  BooksCrudView,
  BooksView,
  BookView,
  LoginView,
  NotFoundView,
  OrdersView,
  PublishersCrudView,
  PublisherView,
  RequestPasswordResetConfirmView,
  RequestPasswordResetView,
  ResetPasswordView,
  TagsCrudView,
  UsersCrudView,
} from '@/views';
import qs from 'qs';
import { nextTick } from 'vue';
import { createRouter, createWebHistory, type LocationQuery } from 'vue-router';
import { isUserHasPermissions, routeFallback } from './permissions-resolver';
import { resolveTransition } from './transition-resolver';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Main',
      component: BooksView,
      meta: {
        title: 'Книжный Фонд',
      },
    },
    {
      path: '/about-us',
      name: 'AboutUs',
      component: AboutUs,
      meta: {
        title: 'О Нас',
      },
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
      path: '/reset-password',
      name: 'ResetPasswordRequest',
      component: RequestPasswordResetView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 2,
      },
    },
    {
      path: '/reset-password/:pathMatch(.{40})',
      name: 'ResetPassword',
      component: ResetPasswordView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 5,
      },
    },
    {
      path: '/reset-password-confirm',
      name: 'ResetPasswordConfirm',
      component: RequestPasswordResetConfirmView,
      meta: {
        title: 'Сброс пароля',
        transitionType: 'Auth',
        transitionLevel: 3,
      },
    },
    {
      path: '/admin/books',
      name: 'BooksCrud',
      component: BooksCrudView,
      meta: {
        title: '[A] Книги',
        roles: ['ROLE_ADMIN', 'ROLE_USER'],
      },
    },
    {
      path: '/admin/publishers',
      name: 'PublishersCrud',
      component: PublishersCrudView,
      meta: {
        title: '[A] Издатели',
        roles: ['ROLE_ADMIN', 'ROLE_USER'],
      },
    },
    {
      path: '/admin/authors',
      name: 'AuthorsCrud',
      component: AuthorsCrudView,
      meta: {
        title: '[A] Авторы',
        roles: ['ROLE_ADMIN', 'ROLE_USER'],
      },
    },
    {
      path: '/admin/tags',
      name: 'TagsCrud',
      component: TagsCrudView,
      meta: {
        title: '[A] Жанры',
        roles: ['ROLE_ADMIN', 'ROLE_USER'],
      },
    },
    {
      path: '/admin/users',
      name: 'UsersCrud',
      component: UsersCrudView,
      meta: {
        title: '[A] Пользователи',
        roles: ['ROLE_ADMIN'],
      },
    },
    {
      path: '/admin/orders',
      name: 'OrdersView',
      component: OrdersView,
      meta: {
        title: 'Заказы',
        roles: ['ROLE_USER'],
      },
    },
    {
      path: '/book/:slug([a-z0-9-]*)',
      name: 'Book',
      component: BookView,
      meta: {
        title: 'Книга',
      },
    },
    {
      path: '/publisher/:slug([a-z0-9-]*)',
      name: 'Publisher',
      component: PublisherView,
      meta: {
        title: 'Издатель',
      },
    },
    {
      path: '/author/:slug([a-z0-9-]*)',
      name: 'Author',
      component: AuthorView,
      meta: {
        title: 'Автор',
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
  parseQuery(query) {
    return qs.parse(query) as LocationQuery;
  },
  stringifyQuery(query) {
    return qs.stringify(query, { encode: false });
  },
});

router.beforeEach(async (to, from) => {
  to.meta.transition = resolveTransition(to, from);

  if (!to.meta.roles) {
    return undefined;
  }

  const profile = await useGetProfile();

  if (!isUserHasPermissions(profile, to.meta.roles as UserRole[] | undefined)) {
    return routeFallback(profile);
  }
});

router.afterEach(async to => {
  nextTick(() => {
    document.title = to.meta.title ? to.meta.title + ' :: МТЭК' : 'МТЭК';
  });
});

export default router;
