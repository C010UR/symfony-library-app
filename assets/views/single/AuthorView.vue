<template>
  <base-page>
    <div class="wrapper-item" v-if="author !== undefined">
      <base-avatar :size="256" :src="author?.image" />

      <div class="body-item">
        <h1 class="name">{{ author.fullName }}</h1>
        <span class="text" v-if="author.email">
          <el-icon><Message /></el-icon>
          <p>Email:</p>
          <el-link class="link" type="primary" :href="`mailto: ${author.email}`">{{ author.email }}</el-link>
        </span>
        <span class="text" v-if="author.website">
          <el-icon><ChromeFilled /></el-icon>
          <p>Веб-Страница:</p>
          <el-link class="link" type="primary" :href="author.website">{{ author.website }}</el-link>
        </span>
        <span class="text">
          <p>Написанные работы:</p>
        </span>
      </div>
    </div>
    <base-books-view v-if="author" :url="`${ApiUrls.authors}/${slug}/books`" :metaUrl="`${ApiUrls.authors}/books`" />
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseAvatar } from '@/components/tags/base';
import { ElLink, ElIcon } from 'element-plus';
import { Message, ChromeFilled } from '@element-plus/icons-vue';
import { ApiUrls, useGetOne } from '@/composables';
import type { BookAuthor } from '@/composables';
import { ref, computed } from 'vue';
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { BaseBooksView } from '@/components/pages/base';

const author = ref<BookAuthor | undefined>();

const route = useRoute();

const slug = computed(() => {
  const path = route.path.split('/');
  return (path.pop() || path.pop()) ?? '';
});

onMounted(async () => {
  author.value = await useGetOne<BookAuthor>(ApiUrls.authors, slug.value);
});
</script>

<style scoped>
.wrapper-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.body-item {
  padding: 0;
  margin: 0;
  padding-left: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.name {
  margin: 0.5rem 0;
}

.header-text {
  text-align: center;
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

.image {
  flex-shrink: 0;
}

.card {
  width: 50%;
  align-self: center;
}

.column {
  flex-direction: column;
  flex-grow: 1;
  padding: 0 0.5rem;
}

.name {
  margin: 0;
  font-size: 2rem;
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
  .card {
    width: 90%;
  }
}
</style>
