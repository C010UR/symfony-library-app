<template>
  <base-card class="create-card" @click="$emit('click')">
    <div class="icon" :style="iconStyle">
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
import { ref } from 'vue';

export interface Props {
  size?: number;
}

const props = withDefaults(defineProps<Props>(), {
  size: 16,
});

const iconStyle = ref({
  width: props.size + 'rem',
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
  height: 100%;
}
</style>
