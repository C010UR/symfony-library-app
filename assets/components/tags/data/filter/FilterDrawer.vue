<template>
  <div class="wrapper">
    <el-button type="primary" :icon="FilterIcon" @click="handleOpen()" :disabled="disabled">
      Фильтрация & Сортировка
    </el-button>
  </div>

  <el-drawer v-model="drawer" title="Фильтрация & Сортировка" direction="rtl" size="50%" @closed="handleClose()">
    <order-filter v-model="orders" :columns="columns" />
    <data-filter v-model="filters" :columns="columns" />
  </el-drawer>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { ElButton, ElDrawer } from 'element-plus';
import { Filter as FilterIcon } from '@element-plus/icons-vue';
import DataFilter from './DataFilter.vue';
import OrderFilter from './DataOrder.vue';

export interface Props {
  disabled?: boolean;
  columns: FilterOption[];
  modelValue: FilterParams;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits(['update:modelValue']);

const drawer = ref(false);

const filterParams = ref<FilterParams>(props.modelValue);
const orders = ref<Order | undefined>();
const filters = ref<Filter[]>([]);

watch(props, (before, after) => {
  if (before.columns !== after.columns) {
    orders.value = undefined;
    filters.value = [];
  }
});

watch(orders, () => {
  parseOrders();
  emit('update:modelValue', filterParams);
});

watch(filters, () => {
  parseFilters();
  emit('update:modelValue', filterParams);
});

function parseOrders() {
  if (orders.value === undefined) {
    return;
  }

  if (filterParams.value.order === undefined) {
    filterParams.value.order = {};
  }

  filterParams.value.order[orders.value.column] = orders.value.direction;

  // for (const column of orders.value) {
  //   result.order[column.column] = column.direction;
  // }
}

function parseFilters() {
  for (const filter of filters.value) {
    let value: string;

    if (Array.isArray(filter.value)) {
      if (filter.value[0] instanceof Date) {
        value = (filter.value as Date[]).map(value => value.toISOString().split('T')[0]).join(',');
      } else {
        value = filter.value.join(',');
      }
    } else if (filter.value instanceof Date) {
      value = filter.value.toISOString().split('T')[0];
    } else {
      value = String(filter.value);
    }

    if (filterParams.value[filter.column] === undefined) {
      filterParams.value[filter.column] = {};
    }

    if (filter.operator !== undefined) {
      filterParams.value[filter.column][filter.operator] = value;
    }
  }
}

function handleOpen() {
  drawer.value = true;
}

function handleClose() {
  drawer.value = false;
}
</script>

<style scoped>
.wrapper {
  margin: 0 1rem;
  display: flex;
  justify-content: end;
}

.flex-row {
  display: flex;
  flex-direction: row;
  align-items: center;
}
</style>
