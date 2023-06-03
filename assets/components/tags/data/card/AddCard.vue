<template>
  <base-card class="create-card" @click="$emit('click')">
    <div class="icon" :style="{ height: size + 'rem', width: '100%' }">
      <el-icon color="var(--el-color-success)" size="100">
        <plus />
      </el-icon>
    </div>
  </base-card>
</template>

<script setup lang="ts">
import { ElIcon } from 'element-plus';
import { Plus } from '@element-plus/icons-vue';
import { BaseCard } from '@/components/tags/base';

export interface Props {
  size?: number;
}

withDefaults(defineProps<Props>(), {
  size: 20,
});

defineEmits(['click']);
</script>

<style scoped>
.create-card {
  cursor: pointer;
}

.create-card .icon {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
