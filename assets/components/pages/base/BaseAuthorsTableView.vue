<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" :disabled="isNoData" />

  <create-author-form v-model="isCreateFormOpen" @submit="submitCreate" />
  <update-author-form v-model="isUpdateFormOpen" @submit="submitUpdate" :entity="updateEntity" />

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
          <div class="expanded-container">
            <span class="text" v-if="props.row.email">
              <el-icon><Message /></el-icon>
              <p>Email:</p>
              <el-link class="link" type="primary" :href="`mailto: ${props.row.email}`">{{ props.row.email }}</el-link>
            </span>
            <span class="text" v-if="props.row.website">
              <el-icon><ChromeFilled /></el-icon>
              <p>Веб-Страница:</p>
              <el-link class="link" type="primary" :href="props.row.website">{{ props.row.website }}</el-link>
            </span>
          </div>
        </template>
      </el-table-column>
    </template>
    <el-table-column label="Изображение">
      <template #default="prop">
        <base-avatar :size="32" :src="prop.row.image" class="avatar" />
      </template>
    </el-table-column>
    <el-table-column label="Ф.И.О.">
      <template #default="prop">
        <el-link
          class="row-text"
          type="primary"
          :underline="false"
          @click="$router.push({ name: 'Author', params: { slug: prop.row.slug } })"
        >
          {{ prop.row.fullName }}
        </el-link>
      </template>
    </el-table-column>
  </table-list>

  <data-pagination v-model="pagination" :disabled="isNoData" />
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BaseAvatar } from '@/components/tags/base';
import { DataPagination, FilterDrawer, TableList } from '@/components/tags/data';
import { CreateAuthorForm, UpdateAuthorForm } from '@/components/tags/form';
import type { ApiMeta, ApiParams, ApiUrl, BookAuthor, Filter, FilterOption, Order, RouteParams } from '@/composables';
import {
  ApiUrls,
  useChangeQuery,
  useDelete,
  useGetAll,
  useGetMeta,
  useParseApiParams,
  useParseParams,
} from '@/composables';
import { ChromeFilled, Message } from '@element-plus/icons-vue';
import { ElIcon, ElLink, ElMessageBox, ElTableColumn } from 'element-plus';
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
  url: ApiUrls.authors,
  metaUrl: ApiUrls.authors,
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
const data = ref<BookAuthor[] | undefined>();
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
  const _data = await useGetAll<BookAuthor>(props.url, parsedParams as RouteParams);

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
const updateEntity = ref<BookAuthor | undefined>();

async function handleDelete(author: BookAuthor) {
  if (author.isDeleted) {
    await useDelete(props.url, author);
    await getData();
    return;
  }

  ElMessageBox.alert(`Вы уверены, что хотите удалить автора '${author.fullName}'?`, `Удаление автора`, {
    confirmButtonText: 'Да',
    cancelButtonText: 'Нет',
    type: 'warning',
    async callback(action: string) {
      if (action === 'confirm') {
        if (await useDelete(props.url, author)) {
          popup('success', 'Автор успешно удален!');
        }

        await getData();
      }
    },
  });
}

function handleUpdate(author: BookAuthor) {
  updateEntity.value = { ...author };
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

.row-text {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  text-overflow: ellipsis;
}

.avatar {
  flex-shrink: 0;
}

.link {
  font-size: 1.125rem;
  margin: 0.5rem 0;
}

.text {
  font-size: 1.125rem;
  display: flex;
  flex-direction: row;
  margin: 0.5rem 0;
  align-items: center;
}

.text > * {
  margin: 0;
  margin-right: 0.3rem;
}
</style>
