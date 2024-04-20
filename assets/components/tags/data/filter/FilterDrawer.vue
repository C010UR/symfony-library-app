<template>
  <div class="wrapper">
    <el-button type="primary" :icon="FilterIcon" @click="handleOpen()" :disabled="disabled">
      Filter & Order
    </el-button>
  </div>

  <el-drawer
    v-model="drawer"
    title="Filter & Order"
    direction="rtl"
    :size="drawerSize"
    @closed="handleClose()"
  >
    <el-scrollbar class="body" :wrap-style="bodyStyle" noresize>
      <order-filter
        :model-value="orders"
        @update:model-value="
      (_orders: Order[] | undefined) =>
      $emit('update:orders', _orders)"
        :columns="columns"
      />
      <data-filter
        :model-value="filters"
        @update:model-value="(_filters: Filter[] | undefined) => $emit('update:filters', _filters)"
        :columns="columns"
      />
    </el-scrollbar>
  </el-drawer>
</template>

<script setup lang="ts">
import type { Filter, FilterOption, Order } from '@/composables';
import { Filter as FilterIcon } from '@element-plus/icons-vue';
import { ElButton, ElDrawer, ElScrollbar } from 'element-plus';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import DataFilter from './DataFilter.vue';
import OrderFilter from './DataOrder.vue';

export interface Props {
  disabled?: boolean;
  columns: FilterOption[];
  orders: Order[] | undefined;
  filters: Filter[] | undefined;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:orders', orders: Order | undefined): void;
  (e: 'update:filters', filters: Filter[] | undefined): void;
}>();

const bodyStyle = ref({ padding: '1rem' });

const drawer = ref(false);
const drawerSize = ref<'100%' | '50%' | '65%' | '85%'>('50%');

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
  if (window.innerWidth <= 768) {
    drawerSize.value = '100%';
  } else if (window.innerWidth <= 992) {
    drawerSize.value = '85%';
  } else if (window.innerWidth <= 1200) {
    drawerSize.value = '65%';
  } else {
    drawerSize.value = '50%';
  }
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

.body {
  margin: 0;
  padding: 0;
}
</style>
