<template>
  <base-page>
    <template #default="scrollBarRef">
      <base-publishers-table-view
        can-create
        can-update
        can-delete
        @update="() => scrollBarRef.scrollbar?.setScrollTop(0)"
      />
    </template>
  </base-page>
</template>

<script setup lang="ts">
import { BasePage, BasePublishersTableView } from '@/components/pages';
</script>
