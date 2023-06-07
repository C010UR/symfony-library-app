<template>
  <div class="wrapper">
    <el-button type="primary" :icon="FilterIcon" @click="handleOpen()" :disabled="disabled">
      Фильтрация & Сортировка
    </el-button>
  </div>

  <el-drawer
    v-model="drawer"
    title="Фильтрация & Сортировка"
    direction="rtl"
    :size="drawerSize"
    @closed="handleClose()"
  >
    <order-filter
      :model-value="orders"
      @update:model-value="(_orders: Order | undefined) => $emit('update:orders',
    _orders)"
      :columns="columns"
    />
    <data-filter
      :model-value="filters"
      @update:model-value="(_filters: Filter[] | undefined) => $emit('update:filters', _filters)"
      :columns="columns"
    />
  </el-drawer>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { ElButton, ElDrawer } from 'element-plus';
import { Filter as FilterIcon } from '@element-plus/icons-vue';
import DataFilter from './DataFilter.vue';
import OrderFilter from './DataOrder.vue';
import type { Filter, FilterOption, Order } from '@/composables';
import { onMounted } from 'vue';
import { onUnmounted } from 'vue';

export interface Props {
  disabled?: boolean;
  columns: FilterOption[];
  orders: Order | undefined;
  filters: Filter[] | undefined;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:orders', orders: Order | undefined): void;
  (e: 'update:filters', filters: Filter[] | undefined): void;
}>();

const drawer = ref(false);
const drawerSize = ref<'100%' | '50%'>('50%');

watch(props, (before, after) => {
  if (before.columns !== after.columns) {
    emit('update:filters', undefined);
    emit('update:orders', undefined);
  }
});

function handleOpen() {
  drawer.value = true;
}

function handleClose() {
  drawer.value = false;
}

function setDrawerSize() {
  drawerSize.value = window.innerWidth <= 992 ? '100%' : '50%';
}

onMounted(() => {
  window.addEventListener('resize', setDrawerSize);
});

onUnmounted(() => {
  window.removeEventListener('resize', setDrawerSize);
});
</script>

<style scoped>
.wrapper {
  margin: 0 1rem 0.5rem 1rem;
  display: flex;
  justify-content: end;
}

.flex-row {
  display: flex;
  flex-direction: row;
  align-items: center;
}
</style>
