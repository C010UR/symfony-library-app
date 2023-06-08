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

      <card-list :disabled="false" :skeletons="20" v-if="author.books && author.books.length > 0">
        <base-card v-for="entity in author.books" :key="entity.id" class="card">
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
            <div class="list">
              <p class="list-title">{{ entity.tags.length > 1 ? 'Жанры' : 'Жанр' }}:</p>
              <el-tag v-for="tag in entity.tags" :key="tag.id" class="list-item">
                {{ tag.name }}
              </el-tag>
            </div>
          </div>
        </base-card>
      </card-list>
    </div>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseAvatar, BaseCard, BaseImage } from '@/components/tags/base';
import { ElLink, ElTag, ElIcon } from 'element-plus';
import { Message, ChromeFilled } from '@element-plus/icons-vue';
import { ApiUrls, useGetOne } from '@/composables';
import type { BookAuthor } from '@/composables';
import { ref } from 'vue';
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { CardList } from '@/components/tags/data';

const author = ref<BookAuthor | undefined>();

const route = useRoute();

onMounted(async () => {
  const path = route.path.split('/');
  const slug = (path.pop() || path.pop()) ?? '';

  author.value = await useGetOne<BookAuthor>(ApiUrls.authors, slug);
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
