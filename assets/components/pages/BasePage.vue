<template>
  <div class="wrapper">
    <div class="header">
      <div class="logo">
        <img src="/images/logo.png" />
      </div>
      <div class="content">
        <div class="top">
          <el-breadcrumb class="breadcrumb" separator="·">
            <el-breadcrumb-item v-for="(label, link) in links" :key="label" :to="{ name: String(link) }">
              {{ label }}
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
    <el-scrollbar class="body" :wrap-style="bodyStyle" noresize>
      <div>
        <slot></slot>
      </div>
    </el-scrollbar>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElBreadcrumb, ElBreadcrumbItem, ElScrollbar } from 'element-plus';
import { DarkSwitch, UserProfile } from '@/components/tags';
import type { UserProfile as UserProfileType } from '@/composables';
import { useRoute, useRouter } from 'vue-router';

interface Links {
  [index: string]: string;
}

const links = ref<Links>({});

const bodyStyle = ref({ padding: '1rem' });

const router = useRouter();
const route = useRoute();
const currentTitle = ref<string>(route.meta.title ? (route.meta.title as string) : '');
const currentName = ref<string>(route.name as string);

function setLinks(profile?: UserProfileType) {
  const commonLinks = {
    ...getRouteLink('Main'),
    ...getRouteLink('AboutUs'),
  };

  console.log(commonLinks);

  if (profile === undefined) {
    links.value = {
      ...commonLinks,
    };
  } else if (profile.roles.includes('ROLE_ADMIN')) {
    links.value = {
      ...commonLinks,
    };
  } else if (profile.roles.includes('ROLE_USER')) {
    links.value = {
      ...commonLinks,
    };
  }

  const link = links.value[currentName.value];
  delete links.value[currentName.value];
  links.value[currentName.value] = link ? link : currentTitle.value;
}

function getRouteLink(name: string) {
  const title = router.getRoutes().find(route => route.name === name)?.meta.title as string;

  if (!title) {
    throw new Error(`Cannot find route '${name}'`);
  }

  const result: {
    [name: string]: string;
  } = {};

  result[name] = title;
  return result;
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

@media only screen and (max-width: 768px) {
  .header .logo {
    background-size: 400%;
    background-image: -webkit-linear-gradient(-45deg, var(--el-color-primary), var(--el-color-primary-light-3));
    background-image: linear-gradient(-45deg, var(--el-color-primary), var(--el-color-primary-light-3));
    min-width: 5rem;
    display: flex;
  }
}
</style>
