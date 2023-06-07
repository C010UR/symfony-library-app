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
import type { BookFull } from '@/composables';

export interface Props {
  value: BookFull;
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
