<template>
  <base-page>
    <template #default="scrollBarRef">
      <base-books-view @update="() => scrollBarRef.scrollbar?.setScrollTop(0)" />
    </template>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage, BaseBooksView } from '@/components/pages';
</script>
