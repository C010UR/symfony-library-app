<template>
  <el-upload
    class="uploader"
    drag
    :auto-upload="false"
    name="image"
    accept="image/*"
    v-model:file-list="fileList"
    :on-change="onChange"
    :on-remove="onRemove"
    :disabled="disabled"
  >
    <div v-if="image || placeholder" :style="uploadBodyStyle" class="upload-body"></div>
    <el-icon :size="40" :class="{ disabled: disabled }"><plus /></el-icon>
    <div v-if="!disabled">
      <div>Drop file here or <em>click to upload</em></div>
    </div>
    <template #tip> Image files with a size less than 8MB </template>
  </el-upload>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { ElUpload, ElIcon, type UploadFile } from 'element-plus';
import type { UploadUserFile } from 'element-plus';
import { Plus } from '@element-plus/icons-vue';
import { popup } from '@/components/tags';

export interface Props {
  placeholder?: string;
  size?: number;
  disabled?: boolean;
  modelValue: UploadUserFile | undefined;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: '',
  size: 16,
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', modelValue: UploadUserFile | undefined): void;
}>();

const fileList = ref<UploadUserFile[] | undefined>([]);
const image = ref<string | undefined>();

const uploadBodyStyle = ref({
  height: props.size + 'rem',
  'background-image': 'url(' + (image.value ? image.value : props.placeholder) + ')',
  'background-size': props.size + 'rem',
});

watchEffect((after: UploadUserFile | undefined) => {
  fileList.value = [];
  if (after !== undefined) {
    fileList.value = [after];
    image.value = after.url;
  } else {
    image.value = undefined;
  }
});

const onChange = (file: UploadFile) => {
  if (file === undefined || file.raw === undefined) {
    emit('update:modelValue', undefined);
    return true;
  }

  if (file.raw.size / 1024 / 1024 > 8) {
    popup('error', 'Image size can not exceed 8MB!');
    fileList.value = [];
    return false;
  }

  emit('update:modelValue', file);

  return true;
};

function onRemove() {
  emit('update:modelValue', undefined);
}
</script>

<style scoped>
em {
  color: var(--el-color-info);
  text-decoration: none;
}

.uploader {
  display: flex;
  flex-direction: column;
  transition: var(--animation-duration);
  width: 100%;
}

.el-icon {
  transition: all var(--animation-duration) ease-out;
}

.disabled {
  transform: rotate(45deg);
}

.upload-body {
  background-repeat: no-repeat;
  background-position: center;
  margin-bottom: '1rem';
}
</style>
