<template>
  <base-page>
    <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" />

    <card-list :disabled="data === undefined" :skeletons="pagination.pageSize">
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
      <base-card v-for="entity in data" :key="entity.id">
        <base-image :src="entity.image" :size="16" class="image" />
        <div class="column info">
          <el-link
            class="name"
            type="primary"
            :underline="false"
            @click="$router.push({ name: 'Book', params: { slug: entity.slug } })"
          >
            {{ entity.name }}
          </el-link>

          <div class="publisher">
            <base-avatar :src="entity.publisher.image" :size="32" class="avatar"></base-avatar>
            <el-link
              class="publisher-name"
              :underline="false"
              @click="$router.push({ name: 'Publisher', params: { slug: entity.publisher.slug } })"
            >
              {{ entity.publisher.name }}
            </el-link>
          </div>
          <div class="list">
            <p class="list-title">{{ entity.authors.length > 1 ? 'Авторы' : 'Автор' }}:</p>

            <el-button
              v-for="author in entity.authors"
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
          <div class="list">
            <p class="list-title">{{ entity.tags.length > 1 ? 'Жанры' : 'Жанр' }}:</p>
            <el-tag v-for="tag in entity.tags" :key="tag.id" class="list-item">
              {{ tag.name }}
            </el-tag>
          </div>
        </div>
        <div class="column description" v-if="entity.description">
          <p class="description-title">Описание:</p>
          <p class="description-text">{{ entity.description }}</p>
        </div>
      </base-card>
    </card-list>

    <data-pagination v-model="pagination" />
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseCard, BaseImage, BaseAvatar } from '@/components/tags/base';
import { FilterDrawer } from '@/components/tags/data/filter';
import { ApiUrls, useChangeQuery, useGetAll, useGetMeta, useParseApiParams, useParseParams } from '@/composables';
import { onMounted, ref, watch } from 'vue';
import { CardList, DataPagination } from '@/components/tags/data';
import type { ApiMeta, BookFull, Filter, Order, FilterOption, ApiParams, RouteParams } from '@/composables';
import { ElTag, ElSkeletonItem, ElLink, ElButton } from 'element-plus';
import { useRoute } from 'vue-router';

const route = useRoute();

const params = useParseApiParams(route.query as ApiParams);

const filters = ref<Filter[] | undefined>(params?.filter);
const orders = ref<Order | undefined>(params?.order);
const pagination = ref<ApiMeta>({
  offset: 0,
  pageSize: 20,
  paginated: true,
  totalCount: 0,
  ...params?.pagination,
});
const columns = ref<FilterOption[]>([]);
const data = ref<BookFull[] | undefined>();
const watchDisabled = ref<boolean>(false);

async function getData() {
  const parsedParams = useParseParams(filters.value, orders.value === undefined ? undefined : [orders.value], {
    offset: pagination.value.offset,
    pageSize: pagination.value.pageSize,
  });

  useChangeQuery(route.path, parsedParams as object);

  data.value = undefined;
  const _data = await useGetAll<BookFull>(ApiUrls.books, parsedParams as RouteParams);

  if (!_data) {
    data.value = [];
    return;
  }

  data.value = _data.data;
  pagination.value = _data.meta;
}

onMounted(async () => {
  columns.value = (await useGetMeta(ApiUrls.books)) ?? [];

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
}

.description-title {
  margin: 0;
}

.description {
  margin: 0.5rem 0;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 10;
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
}

.avatar {
  flex-shrink: 0;
}

.publisher-name {
  font-size: 1.125rem;
  margin-left: 1rem;
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

@media only screen and (max-width: 768px) {
  .column {
    width: 100%;
  }
}
</style>
