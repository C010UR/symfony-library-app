<template>
  <el-option :label="value.name" :value="value.id ?? -1" />
</template>

<script setup lang="ts">
import type { BookTag } from '@/use/api/api';
import { ElOption } from 'element-plus';

export interface Props {
  value: BookTag;
}

defineProps<Props>();
</script>
