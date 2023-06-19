<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" :disabled="isNoData" />

  <create-book-form v-model="isCreateFormOpen" @submit="submitCreate" />
  <update-book-form v-model="isUpdateFormOpen" @submit="submitUpdate" :entity="updateEntity" />

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
    <template #expanded>
      <el-table-column type="expand">
        <template #default="props">
          <book-short-info :book="props.row" />
        </template>
      </el-table-column>
    </template>
    <el-table-column label="Название">
      <template #default="prop">
        <el-link
          class="row-text"
          type="primary"
          :underline="false"
          @click="$router.push({ name: 'Book', params: { slug: prop.row.slug } })"
        >
          {{ prop.row.name }}
        </el-link>
      </template>
    </el-table-column>
    <el-table-column label="Издано в">
      <template #default="prop">
        <span>{{ new Date(prop.row.datePublished).toLocaleDateString() }}</span>
      </template>
    </el-table-column>
    <el-table-column label="Издатель">
      <template #default="prop">
        <span class="row-text">{{ prop.row.publisher.name }}</span>
      </template>
    </el-table-column>
  </table-list>

  <data-pagination v-model="pagination" :disabled="isNoData" />
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BookShortInfo, DataPagination, FilterDrawer, TableList } from '@/components/tags/data';
import { CreateBookForm, UpdateBookForm } from '@/components/tags/form';
import type { ApiMeta, ApiParams, ApiUrl, Book, Filter, FilterOption, Order, RouteParams } from '@/composables';
import {
  ApiUrls,
  useChangeQuery,
  useDelete,
  useGetAll,
  useGetMeta,
  useParseApiParams,
  useParseParams,
} from '@/composables';
import { ElLink, ElMessageBox, ElTableColumn } from 'element-plus';
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
  url: ApiUrls.books,
  metaUrl: ApiUrls.books,
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
const data = ref<Book[] | undefined>();
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
  const _data = await useGetAll<Book>(props.url, parsedParams as RouteParams);

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
const updateEntity = ref<Book | undefined>();

async function handleDelete(book: Book) {
  if (book.isDeleted) {
    await useDelete(props.url, book);
    await getData();
    return;
  }

  ElMessageBox.alert(`Вы уверены, что хотите удалить книгу '${book.name}'?`, `Удаление книги`, {
    confirmButtonText: 'Да',
    cancelButtonText: 'Нет',
    type: 'warning',
    async callback(action: string) {
      if (action === 'confirm') {
        if (await useDelete(props.url, book)) {
          popup('success', 'Книга успешно удалена!');
        }

        await getData();
      }
    },
  });
}

function handleUpdate(book: Book) {
  updateEntity.value = { ...book };
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
.expanded-container {
  margin: 1rem;
  display: flex;
  flex-direction: column;
}
</style>
