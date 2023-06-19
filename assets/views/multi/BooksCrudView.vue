<template>
  <base-page>
    <template #default="scrollBarRef">
      <base-books-table-view can-create can-update can-delete @update="() => scrollBarRef.scrollbar?.setScrollTop(0)" />
    </template>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage, BaseBooksTableView } from '@/components/pages';
</script>
