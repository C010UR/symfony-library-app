<template>
  <div justify="center" class="wrapper">
    <template v-if="disabled">
      <card-list-item v-for="index in skeletons" :key="index">
        <slot name="skeleton"></slot>
      </card-list-item>
    </template>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
import CardListItem from './CardListItem.vue';

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
  align-items: stretch;
}
</style>
