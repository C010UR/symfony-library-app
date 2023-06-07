<template>
  <div justify="center" class="wrapper">
    <template v-if="disabled">
      <slot name="skeleton" v-for="index in skeletons" :key="index"></slot>
    </template>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
export type Props =
  | {
      disabled?: true;
      skeletons: number;
    }
  | {
      disabled?: false;
    };

withDefaults(defineProps<Props>(), {
  disabled: false,
});
</script>

<style scoped>
.wrapper {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: center;
}
</style>
