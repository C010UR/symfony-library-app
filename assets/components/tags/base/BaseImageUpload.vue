<template>
  <el-upload
    ref="upload"
    class="uploader"
    drag
    :auto-upload="false"
    name="image"
    accept="image/*"
    v-model:file-list="fileList"
    :on-change="onChange"
    :on-remove="onRemove"
    :disabled="isLoading"
    list-type="picture"
    :limit="1"
    :on-exceed="onExceed"
  >
    <el-icon :size="40">
      <UploadFilled />
    </el-icon>
    <div>
      <div>Drag & Drop <em>or browse</em> the file</div>
    </div>
    <template #tip> Images smaller than 8MB</template>
  </el-upload>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { UploadFilled } from '@element-plus/icons-vue';
import type { UploadInstance, UploadProps, UploadRawFile, UploadUserFile } from 'element-plus';
import { ElIcon, ElUpload, genFileId } from 'element-plus';
import { ref, watchEffect } from 'vue';

export interface Props {
  isLoading?: boolean;
  modelValue?: UploadUserFile;
}

const props = withDefaults(defineProps<Props>(), {
  isLoading: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', modelValue: UploadUserFile | undefined): void;
}>();

const fileList = ref<UploadUserFile[]>([]);
const upload = ref<UploadInstance>();

watchEffect(() => {
  if (props.modelValue) {
    fileList.value = [props.modelValue];
  } else {
    fileList.value = [];
  }
});

const onExceed: UploadProps['onExceed'] = files => {
  upload.value?.clearFiles();
  const file = files[0] as UploadRawFile;
  file.uid = genFileId();
  upload.value?.handleStart(file);
};

const onChange: UploadProps['onChange'] = file => {
  if (file === undefined || file.raw === undefined) {
    emit('update:modelValue', undefined);
    return true;
  }

  if (file.raw.size / 1024 / 1024 > 8) {
    popup('error', 'Image is too large! Max size is 8MB');
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
  width: 100%;
}
</style>
