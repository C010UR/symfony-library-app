<template>
  <el-avatar :size="size" :src="src" alt="Avatar">
    <el-icon :size="size / 2" color="var(--el-color-primary)">
      <user-filled />
    </el-icon>
  </el-avatar>
</template>

<script setup lang="ts">
import { UserFilled } from '@element-plus/icons-vue';
import { ElAvatar, ElIcon } from 'element-plus';

export interface Props {
  size?: number;
  src?: string;
}

withDefaults(defineProps<Props>(), {
  size: 32,
  src: '',
});
</script>
