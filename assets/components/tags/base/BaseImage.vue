<template>
  <div class="wrapper" :style="{ height: size + 'rem', width: size + 'rem' }">
    <el-image
      v-if="src"
      class="image"
      fit="contain"
      loading="lazy"
      :src="src"
      :style="{ width: 'auto', height: size + 'rem' }"
    />
    <el-empty v-else class="empty" :image-size="150" :style="{ height: size + 'rem' }" />
  </div>
</template>

<script setup lang="ts">
import { ElImage, ElEmpty } from 'element-plus';

export interface Props {
  src?: string;
  size?: number;
}

withDefaults(defineProps<Props>(), {
  src: '',
  size: 14,
});
</script>

<style scoped>
.wrapper {
  margin: 0.5rem;
  display: flex;
  justify-content: center;
}

.empty {
  width: 100%;
}
</style>
