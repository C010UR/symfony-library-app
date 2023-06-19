<template>
  <el-card shadow="hover" :style="{ padding: '0.5rem' }">
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
          <div class="card-body">
            <slot name="skeleton"></slot>
          </div>
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
  margin: 0.5rem 0;
  width: 100%;
  align-self: stretch;
}

.card-body {
  display: flex;
  flex-direction: row;
  align-items: stretch;
  margin: 0;
  padding: 0;
}

@media only screen and (max-width: 992px) {
  .card-body {
    flex-direction: column;
  }
}
</style>
