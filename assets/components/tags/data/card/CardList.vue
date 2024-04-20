<template>
  <div justify="center" class="wrapper">
    <template v-if="loading">
      <slot name="skeleton" v-for="index in skeletons" :key="index"></slot>
    </template>

    <el-empty v-if="empty" description="Empty!" class="no-data" />
    <slot v-if="!loading && !empty"></slot>
  </div>
</template>

<script setup lang="ts">
import { ElEmpty } from 'element-plus';

export type Props =
  | {
      empty?: boolean;
      loading?: true;
      skeletons: number;
    }
  | {
      empty?: boolean;
      loading?: false;
    };

withDefaults(defineProps<Props>(), {
  loading: false,
  empty: false,
});
</script>

<style scoped>
.no-data {
  margin: auto;
}

.wrapper {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: center;
}
</style>
