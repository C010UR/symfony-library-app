<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" />

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
          <div class="expanded-container">
            <div class="expanded-container-2">
              <base-image style="margin: 0" :src="props.row.image" :size="16" class="image" />
              <div class="expanded-container-info">
                <el-link
                  class="name"
                  type="primary"
                  :underline="false"
                  @click="$router.push({ name: 'Book', params: { slug: props.row.slug } })"
                >
                  {{ props.row.name }}
                </el-link>
                <div class="publisher" v-if="props.row.publisher">
                  <el-link
                    class="publisher-name"
                    :underline="false"
                    @click="$router.push({ name: 'Publisher', params: { slug: props.row.publisher.slug } })"
                  >
                    <base-avatar :src="props.row.publisher.image" :size="32" class="avatar" />
                    {{ props.row.publisher.name }}
                  </el-link>
                </div>
                <div class="list" v-if="props.row.authors && props.row.authors.length > 0">
                  <p class="list-title">{{ props.row.authors.length > 1 ? 'Авторы' : 'Автор' }}:</p>

                  <el-button
                    v-for="author in props.row.authors"
                    :key="author.id"
                    class="list-item"
                    type="primary"
                    plain
                    size="small"
                    @click="$router.push({ name: 'Author', params: { slug: author.slug } })"
                  >
                    {{ author.fullName }}
                  </el-button>
                </div>
                <div class="list" v-if="props.row.tags && props.row.tags.length > 0">
                  <p class="list-title">{{ props.row.tags.length > 1 ? 'Жанры' : 'Жанр' }}:</p>
                  <el-tag v-for="tag in props.row.tags" :key="tag.id" class="list-item">
                    {{ tag.name }}
                  </el-tag>
                </div>
              </div>
            </div>
          </div>
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
        <span>{{ new Date(prop.row.datePublished).toDateString() }}</span>
      </template>
    </el-table-column>
    <el-table-column label="Издатель">
      <template #default="prop">
        <span class="row-text">{{ prop.row.publisher.name }}</span>
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
import type { ApiUrl, ApiMeta, Book, Filter, Order, FilterOption, ApiParams, RouteParams } from '@/composables';
import { useRoute } from 'vue-router';
import { BaseImage, BaseAvatar } from '@/components/tags/base';
import { ElTag, ElButton, ElTableColumn, ElLink, ElMessageBox } from 'element-plus';
import { CreateBookForm, UpdateBookForm } from '@/components/tags/form';

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

async function getData() {
  const parsedParams = useParseParams(filters.value, orders.value, {
    offset: pagination.value.offset,
    pageSize: pagination.value.pageSize,
  });

  useChangeQuery(route.path, parsedParams as object);

  data.value = undefined;
  const _data = await useGetAll<Book>(props.url, parsedParams as RouteParams);

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
        await useDelete(props.url, book);
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

.expanded-container-2 {
  display: flex;
  flex-direction: row;
}

.expanded-container-info {
  display: flex;
  flex-direction: column;
}

.image {
  flex-shrink: 0;
}

.list {
  margin: 0.5rem 0;
}

.list-title {
  margin-bottom: 0.5rem;
}

.list-item {
  margin: 0 0.5rem 0.5rem 0;
}

.name {
  margin: 0;
  font-size: 2rem;
}

.row-text {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  text-overflow: ellipsis;
}

.publisher {
  display: flex;
  flex-direction: row;
  align-items: center;
  margin: 0.5rem 0;
  word-wrap: break-word;
}

.avatar {
  flex-shrink: 0;
  margin-right: 1rem;
}

.publisher-name {
  font-size: 1.125rem;
}
</style>
