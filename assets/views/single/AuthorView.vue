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
          <p>Landing Page:</p>
          <el-link class="link" type="primary" :href="author.website">{{ author.website }}</el-link>
        </span>
      </div>
    </div>
    <base-books-view v-if="author" :url="`${ApiUrls.authors}/${slug}/books`" :metaUrl="`${ApiUrls.authors}/books`" />
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseBooksView } from '@/components/pages/base';
import { BaseAvatar } from '@/components/tags/base';
import type { BookAuthor } from '@/composables';
import { ApiUrls, useGetOne } from '@/composables';
import { ChromeFilled, Message } from '@element-plus/icons-vue';
import { ElIcon, ElLink } from 'element-plus';
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

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

@media only screen and (max-width: 768px) {
  .body-item {
    align-items: baseline;
  }
}
</style>
