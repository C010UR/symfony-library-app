<template>
  <base-page>
    <div class="wrapper-item" v-if="book">
      <div class="wrapper-info">
        <base-image :size="20" :src="book?.image" class="image" />

        <div class="body-item">
          <h1 class="name">{{ book.name }}</h1>
          <div class="publisher" v-if="book.publisher">
            <el-link
              class="publisher-name"
              :underline="false"
              @click="$router.push({ name: 'Publisher', params: { slug: book?.publisher.slug } })"
            >
              <base-avatar :src="book.publisher.image" :size="32" class="avatar" />
              {{ book.publisher.name }}
            </el-link>
          </div>
          <span class="text">
            <el-icon><Clock /></el-icon>
            <p>Издана:</p>
            <p>{{ new Date(book.datePublished).toLocaleDateString() }}</p>
          </span>
          <span class="text">
            <el-icon><Document /></el-icon>
            <p>Количество страниц:</p>
            <p>{{ book.pageCount }}</p>
          </span>
          <div class="list">
            <p class="list-title">{{ book.authors.length > 1 ? 'Авторы' : 'Автор' }}:</p>

            <el-button
              v-for="author in book.authors"
              :key="author.id"
              class="list-item"
              type="primary"
              plain
              @click="$router.push({ name: 'Author', params: { slug: author.slug } })"
            >
              {{ author.fullName }}
            </el-button>
          </div>
          <div class="list">
            <p class="list-title">{{ book.tags.length > 1 ? 'Жанры' : 'Жанр' }}:</p>
            <el-tag v-for="tag in book.tags" :key="tag.id" class="list-item" size="large">
              {{ tag.name }}
            </el-tag>
          </div>
        </div>
      </div>
      <div class="wrapper-description" v-if="book.description">
        <pre>{{ book.description }}</pre>
      </div>
    </div>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseAvatar, BaseImage } from '@/components/tags/base';
import type { Book } from '@/composables';
import { ApiUrls, useGetOne } from '@/composables';
import { Clock, Document } from '@element-plus/icons-vue';
import { ElButton, ElIcon, ElLink, ElTag } from 'element-plus';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const book = ref<Book | undefined>();

const route = useRoute();

onMounted(async () => {
  const path = route.path.split('/');
  const slug = (path.pop() || path.pop()) ?? '';

  book.value = await useGetOne<Book>(ApiUrls.books, slug);
});
</script>

<style scoped>
.image {
  flex-shrink: 0;
}

.wrapper-page {
  display: flex;
  flex-direction: column;
  /* align-items: center; */
}

.wrapper-info {
  display: flex;
  flex-direction: row;
  /* align-items: center; */
  margin: 0 5rem;
}

.wrapper-description {
  margin: 0 5rem;
  text-align: justify;
}

.body-item {
  padding: 0;
  margin: 0;
  padding-left: 1rem;
  display: flex;
  flex-direction: column;
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

.list {
  margin: 1rem 0;
}

.list-title {
  font-size: 1.125rem;
  margin-bottom: 0.5rem;
}

.list-item {
  margin: 0 0.5rem 0.5rem 0;
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

@media only screen and (max-width: 768px) {
  .wrapper-description {
    margin: 0;
  }

  .wrapper-info {
    flex-direction: column;
    margin: 0;
  }
}
</style>
