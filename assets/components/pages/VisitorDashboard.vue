<template>
  <base-dashboard :title="title" :links="links">
    <slot></slot>
  </base-dashboard>
</template>

<script setup lang="ts">
import BaseDashboard from './BaseDashboard.vue';

export interface Props {
  title: string;
}

defineProps<Props>();

const links = {
  Главная: 'Main',
  'О Нас': 'AboutUs',
};
</script>
