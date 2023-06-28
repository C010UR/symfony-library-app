<template>
  <div class="wrapper">
    <div class="header">
      <div class="logo">
        <img src="/images/logo.png" />
      </div>
      <div class="content">
        <div class="top">
          <el-breadcrumb class="breadcrumb" separator="·">
            <el-breadcrumb-item v-for="route in routes" :key="route.name" :to="{ name: String(route.name) }">
              {{ route.label }}
            </el-breadcrumb-item>
          </el-breadcrumb>
          <dark-switch class="theme-switch" />
        </div>
        <div class="bottom">
          <user-profile @load-profile="setLinks" />
          <h1>{{ currentTitle }}</h1>
        </div>
      </div>
    </div>
    <el-scrollbar class="body" :wrap-style="bodyStyle" noresize ref="scrollbarRef">
      <div>
        <slot :scrollbar="scrollbarRef"></slot>
      </div>
    </el-scrollbar>
  </div>
</template>

<script setup lang="ts">
import { DarkSwitch, UserProfile } from '@/components/tags';
import type { UserProfile as UserProfileType } from '@/composables';
import { ElBreadcrumb, ElBreadcrumbItem, ElScrollbar } from 'element-plus';
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

interface Link {
  name: string;
  label: string;
}

const routes = ref<Link[]>([]);

const bodyStyle = ref({ padding: '1rem' });

const router = useRouter();
const route = useRoute();
const currentTitle = ref<string>(route.meta.title ? (route.meta.title as string) : '');
const currentName = ref<string>(route.name as string);

const scrollbarRef = ref<InstanceType<typeof ElScrollbar>>();

function setLinks(profile?: UserProfileType) {
  resetRouteLinks();
  addRouteLinks(['Main', 'AboutUs']);

  if (profile) {
    addRouteLinks(['BooksCrud', 'PublishersCrud', 'AuthorsCrud', 'TagsCrud']);
  }

  if (profile && profile.roles?.includes('ROLE_USER')) {
    addRouteLinks(['OrdersView']);
  }

  if (profile && profile.roles?.includes('ROLE_USER')) {
    addRouteLinks(['UsersCrud']);
  }

  moveCurrentRoute();
}

function resetRouteLinks() {
  routes.value.splice(0);
}

function addRouteLinks(names: string[]) {
  for (const name of names) {
    const label = router.getRoutes().find(route => route.name === name)?.meta.title as string;

    if (!label) {
      throw new Error(`Cannot find route '${name}'`);
    }

    routes.value.push({ name, label });
  }
}

function moveCurrentRoute() {
  const index = routes.value.findIndex(item => item.name === currentName.value);

  if (index === -1) {
    return;
  }

  const last = routes.value.splice(index, 1);

  routes.value.push(last[0]);
}
</script>

<style scoped>
.wrapper {
  width: 100vw;
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.header {
  min-width: 100vw;
  z-index: 4;
  display: flex;
  flex-direction: row;
}

.header .logo {
  background-size: 400%;
  background-image: -webkit-linear-gradient(-45deg, var(--el-color-primary), var(--el-color-primary-light-3));
  background-image: linear-gradient(-45deg, var(--el-color-primary), var(--el-color-primary-light-3));
  min-width: 8rem;
  display: flex;
}

.header .logo img {
  filter: brightness(0) invert(1);
  margin: auto;
  height: 4rem;
}

.dark .header {
  background-size: 400%;
  background-image: -webkit-linear-gradient(-45deg, var(--el-color-primary-light-9), var(--el-color-primary-light-7));
  background-image: linear-gradient(-45deg, var(--el-color-primary-light-9), var(--el-color-primary-light-7));
}

.header .content {
  flex-grow: 1;
  box-shadow: var(--el-box-shadow-light);
  padding: 0.75rem 1.33rem 0.33rem 1.33rem;
  background-color: var(--el-bg-color);
  display: flex;
  flex-direction: column;
}

.header .content .breadcrumb {
  margin-bottom: 0.5rem;
}

.header .content .top {
  height: 1.2rem;
  display: flex;
  flex-direction: row;
}

.header .content .bottom {
  height: 3rem;
  display: flex;
  flex-direction: row;
  align-items: center;
}

.header .content h1 {
  margin-left: 1rem;
}

.body {
  flex: 1;
  background-color: var(--el-bg-color);
  margin: 0;
  padding: 0;
}

.theme-switch {
  margin-right: 0;
  margin-left: auto;
}

.top {
  margin-bottom: 0.25rem;
}

@media only screen and (max-width: 768px) {
  .header {
    flex-direction: column;
  }
}
</style>
