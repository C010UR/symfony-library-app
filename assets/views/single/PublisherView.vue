<template>
  <base-page>
    <div class="wrapper-item" v-if="publisher !== undefined">
      <base-avatar :size="256" :src="publisher?.image" />

      <div class="body-item">
        <h1 class="name">{{ publisher.name }}</h1>
        <span class="text">
          <el-icon><Location /></el-icon>
          <p>Адрес:</p>
          <p>{{ publisher.address }}</p>
        </span>
        <span class="text">
          <el-icon><Message /></el-icon>
          <p>Email:</p>
          <el-link class="link" type="primary" :href="`mailto: ${publisher.email}`">{{ publisher.email }}</el-link>
        </span>
        <span class="text">
          <el-icon><ChromeFilled /></el-icon>
          <p>Веб-Страница:</p>
          <el-link class="link" type="primary" :href="publisher.website">{{ publisher.website }}</el-link>
        </span>
      </div>
    </div>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage } from '@/components/pages';
import { BaseAvatar } from '@/components/tags/base';
import { ElLink, ElIcon } from 'element-plus';
import { Message, ChromeFilled, Location } from '@element-plus/icons-vue';
import { ApiUrls, useGetOne } from '@/composables';
import type { BookPublisher } from '@/composables';
import { ref } from 'vue';
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';

const publisher = ref<BookPublisher | undefined>();

const route = useRoute();

onMounted(async () => {
  const path = route.path.split('/');
  const slug = (path.pop() || path.pop()) ?? '';

  publisher.value = await useGetOne<BookPublisher>(ApiUrls.publishers, slug);
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

@media only screen and (max-width: 768px) {
  .description {
    width: 90%;
  }
}
</style>
