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
import type { ApiMeta } from '@/use/api/api';
import { ElPagination } from 'element-plus';

export interface Props {
  modelValue: ApiMeta;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits(['change', 'update:modelValue']);

const handleSizeChange = (value: number) => {
  if (value === props.modelValue.pageSize) {
    return;
  }

  const meta: ApiMeta = { ...props.modelValue };
  meta.pageSize = value;

  emit('update:modelValue', meta);
  emit('change');
};

const handleCurrentChange = (value: number) => {
  const offset = (value - 1) * props.modelValue.pageSize;

  if (offset === props.modelValue.offset) {
    return;
  }

  const meta: ApiMeta = { ...props.modelValue };
  meta.offset = offset;

  emit('update:modelValue', meta);
  emit('change');
};
</script>

<style scoped>
.wrapper {
  margin: 0 1rem;
  display: flex;
  justify-content: end;
}
</style>
