<template>
  <base-page>
    <template #default="scrollBarRef">
      <base-orders-table-view @update="() => scrollBarRef.scrollbar?.setScrollTop(0)" />
    </template>
  </base-page>
</template>

<script setup lang="ts">
import { BaseOrdersTableView, BasePage } from '@/components/pages';
</script>
