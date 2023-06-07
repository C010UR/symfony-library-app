<template>
  <div class="order" v-if="orderableColumns.length > 0">
    <div class="order-header">
      <h1 class="header-text">Упорядочить:</h1>
      <el-switch v-model="toggle" />
    </div>
    <vue-draggable ghost-class="ghost" class="order-container" animation="150" v-model="orderList" v-if="toggle">
      <div class="order-form" v-for="order in orderList" :key="order.id">
        <p class="order-label">{{ order.label !== undefined ? order.label : order.column }}</p>
        <el-switch
          v-model="order.direction"
          size="large"
          inline-prompt
          :active-icon="Top"
          :inactive-icon="Bottom"
          active-value="ASC"
          inactive-value="DESC"
          active-color="var(--el-color-primary-light-5)"
          class="order-switch"
        />
      </div>
    </vue-draggable>
  </div>
</template>

<script setup lang="ts">
import { ElSwitch } from 'element-plus';
import { Top, Bottom } from '@element-plus/icons-vue';
import { computed, ref } from 'vue';
import type { FilterOption, Order } from '@/composables';
import { VueDraggable } from 'vue-draggable-plus';
import { watch } from 'vue';
import { reactive } from 'vue';

export interface Props {
  columns: FilterOption[];
  modelValue: Order[] | undefined;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', orders: Order[] | undefined): void;
}>();

interface OrderListItem extends Order {
  id: number;
  label?: string;
}

let orderList = reactive<OrderListItem[]>([]);

const orderableColumns = computed(() => {
  return props.columns.filter(element => element.isOrderable);
});

function setOrderList() {
  orderList.splice(0);

  if (props.modelValue === undefined) {
    let counter = 0;
    orderableColumns.value.forEach(column => {
      orderList.push({ column: column.name, direction: 'ASC', label: column.label, id: counter++ });
    });
  } else {
    let counter = 0;
    props.modelValue.forEach(column => {
      orderList.push({
        id: counter++,
        column: column.column,
        direction: column.direction,
        label: orderableColumns.value.find(column2 => column.column === column2.name)?.label,
      });
    });

    const orderable: OrderListItem[] = [];
    orderableColumns.value.forEach(column => {
      orderable.push({ column: column.name, direction: 'ASC', label: column.label, id: counter++ });
    });

    orderable.forEach(column => {
      const matching = orderList.find(column2 => column.column === column2.column);

      if (!matching) {
        orderList.push(column);
      }
    });
  }
}

// watch(
//   () => props.modelValue,
//   () => {
//     console.trace('updated');
//     if (!watchDisabled.value) {
//       watchDisabled.value = true;
//       setOrderList();
//       watchDisabled.value = false;
//     }
//   },
// );

setOrderList();

watch(orderList, after => {
  if (after.length === 0) {
    emit('update:modelValue', undefined);
    return;
  }

  const result: Order[] = [];

  for (const order of after) {
    result.push({
      column: order.column,
      direction: order.direction,
    });
  }

  emit('update:modelValue', result);
});

const toggle = ref<boolean>(props.modelValue ? true : false);

watch(toggle, after => {
  if (!after) {
    orderList.splice(0);
  } else {
    setOrderList();
  }
});
</script>

<style scoped>
.order-header {
  display: flex;
  flex-direction: row;
  align-items: baseline;
}

.order-form {
  display: flex;
  flex: 1;
  flex-direction: row;
  align-items: center;
  border-radius: var(--el-border-radius-base);
  color: var(--el-color-primary);
  background-color: var(--el-color-primary-light-9);
  border: 1px solid var(--el-color-primary-light-5);
  padding: 0.5rem;
  cursor: move;
  /* max-width: 20rem; */
}

.order-form:hover {
  background-color: var(--el-color-primary);
  color: var(--el-color-white);
}

.header-text {
  margin-right: 1rem;
}

.order-label {
  margin-left: 1rem;
}

.order-switch {
  margin-right: 1rem;
  margin-left: auto;
}

.order-container {
  display: grid;
  gap: 10px;
}
</style>
