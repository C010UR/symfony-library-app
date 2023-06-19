<template>
  <div class="book-wrapper" v-if="book">
    <base-image style="margin: 0" :src="book.image" :size="16" class="image" />
    <div class="book-info-wrapper">
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
    </div>
  </div>
</template>

<script setup lang="ts">
import { ElLink, ElButton, ElTag } from 'element-plus';
import { BaseAvatar, BaseImage } from '@/components/tags/base';
import type { Book } from '@/composables/api';

export interface Props {
  book?: Book;
}

defineProps<Props>();
</script>

<style scoped>
.book-wrapper {
  display: flex;
  flex-direction: row;
}

.book-info-wrapper {
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

@media only screen and (max-width: 992px) {
  .book-wrapper {
    flex-direction: column;
  }
}
</style>
