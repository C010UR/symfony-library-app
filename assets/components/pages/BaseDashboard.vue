<template>
  <div class="wrapper">
    <div class="header">
      <div class="logo">
        <img src="/images/logo.png" />
      </div>
      <div class="content">
        <div class="top">
          <el-breadcrumb class="breadcrumb" separator="·">
            <el-breadcrumb-item v-for="(link, label) in _links" :key="label" :to="{ name: link }">
              {{ label }}
            </el-breadcrumb-item>
          </el-breadcrumb>
          <dark-switch style="margin-right: 0; margin-left: auto" />
        </div>
        <div class="bottom">
          <user-profile />
          <h1>{{ title }}</h1>
        </div>
      </div>
    </div>
    <el-scrollbar class="body" noresize>
      <div>
        <slot></slot>
      </div>
    </el-scrollbar>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElBreadcrumb, ElBreadcrumbItem, ElScrollbar } from 'element-plus';
import { UserProfile, DarkSwitch } from '@/components/tags/index.js';

interface Links {
  [index: string]: string;
}

export interface Props {
  title?: string;
  links: Links;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Заголовок',
});

const _links = ref({ ...props.links });

if (props.links && props.title) {
  const link = _links.value[props.title];
  delete _links.value[props.title];
  _links.value[props.title] = link;
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
  min-width: 10rem;
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
  padding: 1rem;
}

@media only screen and (max-width: 768px) {
  .header {
    flex-direction: column;
  }
}
</style>
