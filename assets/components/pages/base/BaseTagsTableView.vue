<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" :disabled="isNoData" />

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
    <el-table-column label="Name">
      <template #default="prop">
        <span class="row-text"> {{ prop.row.name }} </span>
      </template>
    </el-table-column>
  </table-list>

  <data-pagination v-model="pagination" :disabled="isNoData" />
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { DataPagination, TableList, FilterDrawer } from '@/components/tags/data';
import { CreateTagForm, UpdateTagForm } from '@/components/tags/form';
import type { ApiMeta, ApiParams, ApiUrl, BookTag, Filter, FilterOption, Order, RouteParams } from '@/composables';
import {
  ApiUrls,
  useChangeQuery,
  useDelete,
  useGetAll,
  useGetMeta,
  useParseApiParams,
  useParseParams,
} from '@/composables';
import { ElMessageBox, ElTableColumn } from 'element-plus';
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

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
const data = ref<BookTag[] | undefined>();
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
  const _data = await useGetAll<BookTag>(props.url, parsedParams as RouteParams);

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

const isCreateFormOpen = ref<boolean>(false);
const isUpdateFormOpen = ref<boolean>(false);
const updateEntity = ref<BookTag | undefined>();

async function handleDelete(tag: BookTag) {
  if (tag.isDeleted) {
    await useDelete(props.url, tag);
    await getData();
    return;
  }

  ElMessageBox.alert(`Are you sure you want to delete '${tag.name}'?`, `Delete Genre`, {
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    type: 'warning',
    async callback(action: string) {
      if (action === 'confirm') {
        if (await useDelete(props.url, tag)) {
          popup('success', 'Genre deleted successfully!');
        } else {
          popup('error', 'An error occurred during the Genre deletion');
        }

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
