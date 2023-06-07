<template>
  <el-option :label="value.fullName" :value="value.id ?? -1">
    <div class="item">
      <base-image :src="value.image" :size="1.5"></base-image>
      <span>{{ value.fullName }}</span>
    </div>
  </el-option>
</template>

<script setup lang="ts">
import { ElOption } from 'element-plus';
import { BaseImage } from '@/components/tags/base';
import type { BookAuthor } from '@/composables';

export interface Props {
  value: BookAuthor;
}

defineProps<Props>();
</script>

<style scoped>
.item {
  display: flex;
  flex-direction: row;
  align-items: center;
}
</style>
