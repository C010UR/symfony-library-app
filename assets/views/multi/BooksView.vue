<template>
  <base-page>
    <template #default="scrollBarRef">
      <base-books-view @update="() => scrollBarRef.scrollbar?.setScrollTop(0)" />
    </template>
  </base-page>
</template>

<script setup lang="ts">
import { BaseBooksView, BasePage } from '@/components/pages';
</script>
