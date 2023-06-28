<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" :disabled="isNotLoaded" />

  <create-order-form v-model="isCreateFormOpen" @submit="submitCreate" :book="createEntity" />

  <card-list :skeletons="pagination.pageSize" :empty="isNoData" :loading="isNotLoaded">
    <template #skeleton>
      <base-card :disabled="true">
        <template #skeleton>
          <el-skeleton-item variant="image" class="image skeleton" />
          <div class="column info">
            <el-skeleton-item variant="h1" class="name" />
            <div class="publisher skeleton">
              <base-avatar src="" :size="32" class="avatar"></base-avatar>
              <el-skeleton-item variant="h3" class="publisher-name" />
            </div>
            <div class="list">
              <p class="list-title">Авторы:</p>
              <el-button v-for="item in 3" :key="item" class="list-item" type="primary" plain size="small" disabled>
                Автор
              </el-button>
            </div>
            <div class="list">
              <p class="list-title">Жанры:</p>
              <el-tag v-for="item in 5" :key="item" class="list-item">Жанр</el-tag>
            </div>
          </div>
          <div class="column description">
            <p class="desc-title">Описание:</p>
            <el-skeleton-item variant="p" class="desc" v-for="index in 10" :key="index" />
          </div>
        </template>
      </base-card>
    </template>
    <base-card v-for="book of data" :key="book.id">
      <base-image :src="book.image" :size="16" class="image" />
      <div class="column info">
        <el-link
          class="name"
          type="primary"
          :underline="false"
          @click="$router.push({ name: 'Book', params: { slug: book.slug } })"
        >
          {{ book.name }}
        </el-link>

        <div class="publisher" v-if="book.publisher">
          <el-link
            class="publisher-name"
            :underline="false"
            @click="$router.push({ name: 'Publisher', params: { slug: book.publisher.slug } })"
          >
            <base-avatar :src="book.publisher.image" :size="32" class="avatar" />
            {{ book.publisher.name }}
          </el-link>
        </div>
        <div class="list" v-if="book.authors && book.authors.length > 0">
          <p class="list-title">{{ book.authors.length > 1 ? 'Авторы' : 'Автор' }}:</p>

          <el-button
            v-for="author in book.authors"
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
        <div class="list" v-if="book.tags && book.tags.length > 0">
          <p class="list-title">{{ book.tags.length > 1 ? 'Жанры' : 'Жанр' }}:</p>
          <el-tag v-for="tag in book.tags" :key="tag.id" class="list-item">
            {{ tag.name }}
          </el-tag>
        </div>
        <div class="order">
          <el-button type="primary" class="order-button" @click="handleCreate(book)">Заказать</el-button>
        </div>
      </div>
      <div class="column" v-if="book.description">
        <div class="description">
          <p class="description-title">Описание:</p>
          <pre class="description-text">{{ book.description }}</pre>
        </div>
      </div>
    </base-card>
  </card-list>

  <data-pagination v-model="pagination" :disabled="isNotLoaded" />
</template>

<script setup lang="ts">
import { BaseAvatar, BaseCard, BaseImage } from '@/components/tags/base';
import { CardList, DataPagination, FilterDrawer } from '@/components/tags/data';
import { CreateOrderForm } from '@/components/tags/form';
import type { ApiMeta, ApiParams, ApiUrl, Book, Filter, FilterOption, Order, RouteParams } from '@/composables';
import { ApiUrls, useChangeQuery, useGetAll, useGetMeta, useParseApiParams, useParseParams } from '@/composables';
import { ElButton, ElLink, ElSkeletonItem, ElTag } from 'element-plus';
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

export interface Props {
  url?: ApiUrl | string;
  metaUrl?: ApiUrl | string;
}

const props = withDefaults(defineProps<Props>(), {
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
  pageSize: 10,
  paginated: true,
  totalCount: 0,
  ...params?.pagination,
});
const columns = ref<FilterOption[]>([]);
const data = ref<Book[] | undefined>();
const watchDisabled = ref<boolean>(false);

const isNotLoaded = computed(() => {
  return data.value === undefined;
});

const isNoData = computed(() => {
  return Array.isArray(data.value) && data.value.length === 0;
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
const createEntity = ref<Book | undefined>();

function handleCreate(book: Book) {
  createEntity.value = { ...book };
  isCreateFormOpen.value = true;
}

async function submitCreate() {
  isCreateFormOpen.value = false;
  await getData();
}
</script>

<style scoped>
.image {
  flex-shrink: 0;
}

.image.skeleton {
  width: 16rem;
  height: 16rem;
  margin: 0.5rem;
  align-self: center;
}

.column {
  width: 40%;
  flex-direction: column;
  flex-grow: 1;
  padding: 0 0.5rem;
  margin: 0;
}

.description-title {
  margin: 0;
}

.description {
  margin: 0.5rem 0;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 15;
  -webkit-box-orient: vertical;
  text-align: justify;
  text-overflow: ellipsis;
}

.name {
  margin: 0;
  font-size: 2rem;
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

.list {
  margin: 0.5rem 0;
}

.list-title {
  margin-bottom: 0.5rem;
}

.list-item {
  margin: 0 0.5rem 0.5rem 0;
}

.order {
  margin-top: auto;
  display: flex;
  flex-direction: row;
}

.order-button {
  margin-left: auto;
}

@media only screen and (max-width: 992px) {
  .column {
    width: 100%;
  }
}
</style>
