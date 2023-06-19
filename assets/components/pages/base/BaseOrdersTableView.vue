<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" :disabled="isNoData" />

  <table-list :data="data" withMeta>
    <template #expanded>
      <el-table-column type="expand">
        <template #default="props">
          <div class="expanded-container">
            <book-short-info :book="props.row.book" />
          </div>
        </template>
      </el-table-column>
    </template>
    <el-table-column label="Заказчик">
      <template #default="prop">
        <span class="row-text"> {{ prop.row.fullName }} </span>
      </template>
    </el-table-column>
    <el-table-column label="Телефон" width="120">
      <template #default="prop">
        <span> {{ prop.row.phoneNumber }} </span>
      </template>
    </el-table-column>
    <el-table-column label="Книга">
      <template #default="prop">
        <el-link
          class="row-text"
          type="primary"
          :underline="false"
          @click="$router.push({ name: 'Book', params: { slug: prop.row.book.slug } })"
        >
          {{ prop.row.book.name }}
        </el-link>
      </template>
    </el-table-column>
    <el-table-column label="Количество" width="110">
      <template #default="prop">
        <span> {{ prop.row.quantity }} </span>
      </template>
    </el-table-column>
    <el-table-column label="Дата создания">
      <template #default="prop">
        <span> {{ new Date(prop.row.dateCreated).toLocaleString() }} </span>
      </template>
    </el-table-column>
    <el-table-column label="Выполнен">
      <template #default="prop">
        <div class="completed" v-if="prop.row.dateCompleted">
          <base-profile :user="prop.row.userCompleted" class="completed-user" />
          <span>
            {{ new Date(prop.row.dateCompleted).toLocaleString() }}
          </span>
        </div>
      </template>
    </el-table-column>

    <el-table-column fixed="right" label="Действия" width="100">
      <template #default="scope">
        <el-popconfirm
          v-if="!scope.row.dateCompleted"
          title="Вы уверены, что хотите выполнить этот заказ?"
          @confirm="handleComplete(scope.row)"
        >
          <template #reference>
            <el-button link type="success" size="small"> Выполнить </el-button>
          </template>
        </el-popconfirm>
        <el-popconfirm
          v-else
          title="Вы уверены, что хотите отменить выполнение этого заказ?"
          @confirm="handleComplete(scope.row)"
        >
          <template #reference>
            <el-button link type="danger" size="small"> Отменить </el-button>
          </template>
        </el-popconfirm>
      </template>
    </el-table-column>
  </table-list>

  <data-pagination v-model="pagination" :disabled="isNoData" />
</template>

<script setup lang="ts">
import { FilterDrawer } from '@/components/tags/data/filter';
import {
  ApiUrls,
  useChangeQuery,
  useGetAll,
  useGetMeta,
  useParseApiParams,
  useParseParams,
  useUpdate,
} from '@/composables';
import { onMounted, ref, watch, computed } from 'vue';
import { TableList, DataPagination } from '@/components/tags/data';
import type { ApiUrl, ApiMeta, BookOrder, Filter, Order, FilterOption, ApiParams, RouteParams } from '@/composables';
import { useRoute } from 'vue-router';
import { ElTableColumn, ElButton, ElPopconfirm, ElMessageBox, ElLink } from 'element-plus';
import { popup } from '@/components/tags';
import { BookShortInfo } from '@/components/tags/data';
import { BaseProfile } from '@/components/tags';

export interface Props {
  url?: ApiUrl | string;
  metaUrl?: ApiUrl | string;
}

const props = withDefaults(defineProps<Props>(), {
  url: ApiUrls.orders,
  metaUrl: ApiUrls.orders,
});

const emit = defineEmits<{
  (e: 'update', isUpdated: true): void;
}>();

const route = useRoute();

const params = useParseApiParams(route.query as ApiParams);

const filters = ref<Filter[] | undefined>(params?.filters);
const orders = ref<Order[] | undefined>(params?.orders);
const pagination = ref<ApiMeta>({
  offset: 0,
  pageSize: 20,
  paginated: true,
  totalCount: 0,
  ...params?.pagination,
});
const columns = ref<FilterOption[]>([]);
const data = ref<BookOrder[] | undefined>();
const watchDisabled = ref<boolean>(false);

const isNoData = computed(() => {
  return data.value === undefined;
});

async function getData() {
  const parsedParams = useParseParams(filters.value, orders.value, {
    offset: pagination.value.offset,
    pageSize: pagination.value.pageSize,
  });

  useChangeQuery(route.path, parsedParams as object);

  data.value = undefined;
  const _data = await useGetAll<BookOrder>(props.url, parsedParams as RouteParams);

  emit('update', true);

  if (!_data) {
    data.value = [];
    return;
  }

  data.value = _data.data;
  pagination.value = _data.meta;
}

onMounted(async () => {
  columns.value = (await useGetMeta(props.metaUrl)) ?? [];

  watchDisabled.value = true;
  await getData();
  watchDisabled.value = false;
});

watch([filters, orders, pagination], async () => {
  if (!watchDisabled.value) {
    watchDisabled.value = true;
    await getData();
    watchDisabled.value = false;
  }
});

async function handleComplete(order: BookOrder) {
  ElMessageBox.alert(
    `Вы уверены, что хотите '${order.dateCompleted ? 'отменить выполнение заказа' : 'выполнить заказ'}' книги '${
      order.book.name
    }' в количестве ${order.quantity} штук${order.quantity === 1 ? 'а' : ''} для ${order.fullName}?`,
    `Выполнение заказа`,
    {
      confirmButtonText: 'Да',
      cancelButtonText: 'Нет',
      type: 'warning',
      async callback(action: string) {
        if (action === 'confirm') {
          if (await useUpdate(props.url, order.id, {})) {
            popup('success', `Заказ успешно ${order.dateCompleted ? 'отменен' : 'выполнен'}!`);
          }
          await getData();
        }
      },
    },
  );
}
</script>

<style scoped>
.row-text {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  text-overflow: ellipsis;
}

.completed {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.completed-user {
  margin-right: 0.3rem;
}
</style>
