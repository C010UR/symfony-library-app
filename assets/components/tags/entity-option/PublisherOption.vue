<template>
  <el-option :label="value.name" :value="value.id ?? -1">
    <div class="item">
      <base-image :src="value.image" :size="1.5"></base-image>
      <span>{{ value.name }}</span>
    </div>
  </el-option>
</template>

<script setup lang="ts">
import { ElOption } from 'element-plus';
import { BaseImage } from '@/components/tags/base';
import type { BookPublisher } from '@/composables';

export interface Props {
  value: BookPublisher;
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
