<template>
  <visitor-dashboard title="Главная">
    <filter-drawer :columns="columns" v-model="filterParams" />

    <card-list :disabled="data.length === 0" :skeletons="paginationParams.pageSize">
      <template #skeleton>
        <base-card :disabled="true">
          <template #skeleton>
            <div style="display: flex; flex-direction: row">
              <el-skeleton-item variant="image" style="width: 16rem; height: 16rem; flex-shrink: 0" />
              <div style="flex-direction: column; margin-left: 1rem">
                <el-skeleton-item variant="h1" style="margin: 0" />
                <div style="display: flex; flex-direction: row; align-items: center">
                  <base-avatar src="" :size="32" style="flex-shrink: 0"></base-avatar>
                  <el-skeleton-item variant="h3" style="margin-left: 1rem" />
                </div>
                <div>
                  <p style="margin-bottom: 0.5rem">Авторы:</p>
                  <el-tag v-for="item in 3" :key="item" style="margin-right: 0.5rem; margin-bottom: 0.5rem"
                    >Автор</el-tag
                  >
                </div>
                <div>
                  <p style="margin-bottom: 0.5rem">Жанры:</p>
                  <el-tag v-for="item in 5" :key="item" style="margin-right: 0.5rem; margin-bottom: 0.5rem"
                    >Жанр</el-tag
                  >
                </div>
              </div>
            </div>
          </template>
        </base-card>
      </template>
      <card-list-item v-for="entity in data" :key="entity.id">
        <base-card>
          <base-image :src="entity.image" :size="16" style="flex-shrink: 0" />
          <div style="flex-direction: column; margin-left: 1rem">
            <h1 style="margin: 0">{{ entity.name }}</h1>
            <div style="display: flex; flex-direction: row; align-items: center">
              <base-avatar :src="entity.publisher.image" :size="32" style="flex-shrink: 0"></base-avatar>
              <h3 style="margin-left: 1rem">{{ entity.publisher.name }}</h3>
            </div>
            <div>
              <p style="margin-bottom: 0.5rem">Авторы:</p>
              <el-tag
                v-for="author in entity.authors"
                :key="author.id"
                style="margin-right: 0.5rem; margin-bottom: 0.5rem"
                >{{ author.fullName }}</el-tag
              >
            </div>
            <div>
              <p style="margin-bottom: 0.5rem">Жанры:</p>
              <el-tag v-for="tag in entity.tags" :key="tag.id" style="margin-right: 0.5rem; margin-bottom: 0.5rem">{{
                tag.name
              }}</el-tag>
            </div>
          </div>
        </base-card>
      </card-list-item>
    </card-list>

    <data-pagination v-model="paginationParams" />
  </visitor-dashboard>
</template>

<script setup lang="ts">
import { VisitorDashboard } from '@/components/pages';
import { BaseCard, BaseImage, BaseAvatar } from '@/components/tags/base';
import { FilterDrawer } from '@/components/tags/data/filter';
import { ApiUrls, useGetAll, useGetMeta } from '@/use';
import { onMounted, ref, reactive, watch } from 'vue';
import { CardList, CardListItem, DataPagination } from '@/components/tags/data';
import type { ApiMeta, BookFull } from '@/use/api/api';
import { ElTag, ElSkeletonItem } from 'element-plus';

const filterParams = reactive<FilterParams>({});
const paginationParams = ref<ApiMeta>({
  offset: 0,
  pageSize: 20,
  paginated: true,
  totalCount: 0,
});
const columns = ref<FilterOption[]>([]);
const data = ref<BookFull[]>([]);

function getParams() {
  return {
    ...filterParams,
    offset: paginationParams.value.offset,
    pageSize: paginationParams.value.pageSize,
  };
}

async function getData() {
  data.value = [];
  const _data = await useGetAll<BookFull>(ApiUrls.books, getParams());

  if (!_data) {
    return;
  }

  data.value = _data.data;
  paginationParams.value.offset = _data.meta.offset;
  paginationParams.value.pageSize = _data.meta.pageSize;
  paginationParams.value.paginated = _data.meta.paginated;
  paginationParams.value.totalCount = _data.meta.totalCount;
}

onMounted(async () => {
  columns.value = (await useGetMeta(ApiUrls.books)) ?? [];

  await getData();
});

watch(filterParams, async () => {
  await getData();
});

watch(paginationParams, async () => {
  console.log(paginationParams);
  await getData();
});
</script>
