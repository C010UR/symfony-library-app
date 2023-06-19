<template>
  <el-option :label="value.fullName" :value="value.id ?? -1">
    <div class="item">
      <base-avatar :src="value.image" :size="24" />
      <span class="text">{{ value.fullName }}</span>
    </div>
  </el-option>
</template>

<script setup lang="ts">
import { BaseAvatar } from '@/components/tags/base';
import type { BookAuthor } from '@/composables';
import { ElOption } from 'element-plus';

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

.text {
  margin-left: 0.5rem;
}
</style>
