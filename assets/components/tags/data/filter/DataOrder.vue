<template>
  <div class="order" v-if="orderableColumns.length > 0">
    <div class="order-header">
      <h1 class="margin-right">Упорядочить:</h1>
      <el-switch v-model="toggle" />
    </div>
    <div class="order-form" v-if="toggle">
      <el-select
        :model-value="modelValue?.column"
        @update:model-value="handleColumnChange"
        filterable
        default-first-option
        :disabled="!toggle"
        class="margin-right"
      >
        <el-option v-for="order in orderableColumns" :key="order.name" :value="order.name" :label="order.label" />
      </el-select>
      <el-switch
        :model-value="modelValue?.direction"
        @update:model-value="handleDirectionChange"
        size="large"
        inline-prompt
        :active-icon="Top"
        :inactive-icon="Bottom"
        active-value="ASC"
        inactive-value="DESC"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ElSelect, ElOption, ElSwitch } from 'element-plus';
import { Top, Bottom } from '@element-plus/icons-vue';
import { computed, ref } from 'vue';
import type { FilterOption, Order } from '@/composables';
import { watchEffect } from 'vue';

export interface Props {
  columns: FilterOption[];
  modelValue: Order | undefined;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', orders: Order | undefined): void;
}>();

const toggle = ref<boolean>(false);

watchEffect(() => {
  if (!toggle.value) {
    emit('update:modelValue', undefined);
  }
});

const orderableColumns = computed(() => {
  return props.columns.filter(element => element.isOrderable);
});

function handleColumnChange(value: string) {
  const data: Order = { direction: props.modelValue?.direction ?? 'ASC', column: value };

  emit('update:modelValue', data);
}

function handleDirectionChange(value: string | number | boolean) {
  if (props.modelValue?.column === undefined) {
    return;
  }

  if (typeof value === 'string') {
    const data: Order = { direction: value === 'ASC' ? 'ASC' : 'DESC', column: props.modelValue.column };

    emit('update:modelValue', data);

    return;
  }

  const data: Order = { direction: value ? 'ASC' : 'DESC', column: props.modelValue.column };

  emit('update:modelValue', data);
}
</script>

<style scoped>
.order-header {
  display: flex;
  flex-direction: row;
  align-items: baseline;
}

.order-form {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.margin-right {
  margin-right: 0.5rem;
}
</style>
