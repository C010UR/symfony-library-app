<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" />

  <create-tag-form v-model="isCreateFormOpen" @submit="submitCreate" />
  <update-tag-form v-model="isUpdateFormOpen" @submit="submitUpdate" :entity="updateEntity" />

  <table-list
    :can-create="canCreate"
    :can-update="canUpdate"
    :can-delete="canDelete"
    :data="data"
    withMeta
    @click:delete="handleDelete"
    @click:update="handleUpdate"
    @click:create="handleCreate"
  >
    <el-table-column label="Название">
      <template #default="prop">
        <span class="row-text"> {{ prop.row.name }} </span>
      </template>
    </el-table-column>
  </table-list>

  <data-pagination v-model="pagination" />
</template>

<script setup lang="ts">
import { FilterDrawer } from '@/components/tags/data/filter';
import {
  ApiUrls,
  useChangeQuery,
  useGetAll,
  useDelete,
  useGetMeta,
  useParseApiParams,
  useParseParams,
} from '@/composables';
import { onMounted, ref, watch } from 'vue';
import { TableList, DataPagination } from '@/components/tags/data';
import type { ApiUrl, ApiMeta, BookTag, Filter, Order, FilterOption, ApiParams, RouteParams } from '@/composables';
import { useRoute } from 'vue-router';
import { ElTableColumn, ElMessageBox } from 'element-plus';
import { CreateTagForm, UpdateTagForm } from '@/components/tags/form';

export interface Props {
  canCreate?: boolean;
  canUpdate?: boolean;
  canDelete?: boolean;
  url?: ApiUrl | string;
  metaUrl?: ApiUrl | string;
}

const props = withDefaults(defineProps<Props>(), {
  canCreate: false,
  canUpdate: false,
  canDelete: false,
  url: ApiUrls.tags,
  metaUrl: ApiUrls.tags,
});

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
const data = ref<BookTag[] | undefined>();
const watchDisabled = ref<boolean>(false);

async function getData() {
  const parsedParams = useParseParams(filters.value, orders.value, {
    offset: pagination.value.offset,
    pageSize: pagination.value.pageSize,
  });

  useChangeQuery(route.path, parsedParams as object);

  data.value = undefined;
  const _data = await useGetAll<BookTag>(props.url, parsedParams as RouteParams);

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

const isCreateFormOpen = ref<boolean>(false);
const isUpdateFormOpen = ref<boolean>(false);
const updateEntity = ref<BookTag | undefined>();

async function handleDelete(tag: BookTag) {
  if (tag.isDeleted) {
    await useDelete(props.url, tag);
    await getData();
    return;
  }

  ElMessageBox.alert(`Вы уверены, что хотите удалить жанр '${tag.name}'?`, `Удаление жанра`, {
    confirmButtonText: 'Да',
    cancelButtonText: 'Нет',
    type: 'warning',
    async callback(action: string) {
      if (action === 'confirm') {
        await useDelete(props.url, tag);
        await getData();
      }
    },
  });
}

function handleUpdate(tag: BookTag) {
  updateEntity.value = { ...tag };
  isUpdateFormOpen.value = true;
}

async function submitUpdate() {
  isUpdateFormOpen.value = false;
  await getData();
}

function handleCreate() {
  isCreateFormOpen.value = true;
}

async function submitCreate() {
  isCreateFormOpen.value = false;
  await getData();
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
</style>