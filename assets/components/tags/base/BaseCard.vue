<template>
  <el-card shadow="hover">
    <template #header v-if="$slots.header">
      <template v-if="disabled">
        <el-skeleton animated>
          <template #template>
            <slot name="header-skeleton"></slot>
          </template>
        </el-skeleton>
      </template>
      <template v-else>
        <div>
          <slot name="header"></slot>
        </div>
      </template>
    </template>

    <template v-if="disabled">
      <el-skeleton animated>
        <template #template>
          <slot name="skeleton"></slot>
        </template>
      </el-skeleton>
    </template>
    <template v-else>
      <div class="card-body">
        <slot></slot>
      </div>
    </template>
  </el-card>
</template>

<script setup lang="ts">
import { ElCard, ElSkeleton } from 'element-plus';

export interface Props {
  disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
  disabled: false,
});
</script>

<style scoped>
.el-card {
  margin: 1rem;
  width: 100%;
  align-self: stretch;
}

.card-body {
  display: flex;
  flex-direction: row;
  align-items: start;
}
</style>
