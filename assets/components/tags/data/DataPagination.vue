<template>
  <div class="wrapper">
    <el-pagination
      :current-page="Math.floor(modelValue.offset / modelValue.pageSize) + 1"
      :page-sizes="[5, 10, 20, 30, 50]"
      :page-size="modelValue.pageSize"
      :disabled="disabled"
      :background="false"
      layout="total, sizes, prev, pager, next"
      :total="modelValue.totalCount"
      :hide-on-single-page="false"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    />
  </div>
</template>

<script setup lang="ts">
import type { ApiMeta } from '@/composables';
import { ElPagination } from 'element-plus';

export interface Props {
  modelValue: ApiMeta;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', modelValue: ApiMeta): void;
}>();

const handleSizeChange = (value: number) => {
  if (value === props.modelValue.pageSize) {
    return;
  }
  const meta: ApiMeta = { ...props.modelValue, pageSize: value };
  console.log('2');
  emit('update:modelValue', meta);
};

const handleCurrentChange = (value: number) => {
  const offset = (value - 1) * props.modelValue.pageSize;
  if (offset === props.modelValue.offset) {
    return;
  }
  const meta: ApiMeta = { ...props.modelValue, offset: offset };
  console.log('1');
  emit('update:modelValue', meta);
};
</script>

<style scoped>
.wrapper {
  margin: 0.5rem 1rem 0 1rem;
  display: flex;
  justify-content: end;
}
</style>
