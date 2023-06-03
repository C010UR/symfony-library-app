<template>
  <el-option :label="value.name" :value="value.id ?? -1">
    <div style="display: flex; flex-direction: row; align-items: center">
      <base-image :src="value.image" :size="1.5"></base-image>
      <span>{{ value.name }}</span>
    </div>
  </el-option>
</template>

<script setup lang="ts">
import { ElOption } from 'element-plus';
import { BaseImage } from '@/components/tags/base';
import type { BookPublisher } from '@/use/api/api';

export interface Props {
  value: BookPublisher;
}

defineProps<Props>();
</script>
