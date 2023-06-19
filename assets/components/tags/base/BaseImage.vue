<template>
  <div class="wrapper" :style="wrapperStyle">
    <el-image v-if="src" class="image" fit="contain" loading="lazy" :src="src" :style="imageStyle" />
    <el-empty v-else class="empty" :image-size="150" :style="imageStyle" />
  </div>
</template>

<script setup lang="ts">
import { ElEmpty, ElImage } from 'element-plus';
import { ref } from 'vue';

export interface Props {
  src?: string;
  size?: number;
}

const props = withDefaults(defineProps<Props>(), {
  src: '',
  size: 16,
});

const wrapperStyle = ref({
  height: props.size + 'rem',
  width: props.size + 'rem',
});

const imageStyle = ref({
  width: 'auto',
  height: props.size + 'rem',
});
</script>

<style scoped>
.wrapper {
  margin: 0.5rem;
  display: flex;
  justify-content: center;
  align-self: center;
}

.empty {
  width: 100%;
}
</style>
