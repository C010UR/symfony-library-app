<template>
  <base-form
    type="create"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <el-form ref="formRef" :model="form" :rules="publisherRules" label-position="top">
      <el-form-item label="Logo" prop="image">
        <base-image-upload :isLoading="isLoading" v-model="form.image" />
      </el-form-item>
      <el-form-item label="Name" prop="name">
        <el-input v-model="form.name" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Email" prop="email">
        <el-input v-model="form.email" maxlength="255" show-word-limit clearable :disabled="isLoading" type="email" />
      </el-form-item>
      <el-form-item label="Address" prop="address">
        <el-input v-model="form.address" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Landing Page" prop="website">
        <el-input v-model="form.website" maxlength="255" show-word-limit clearable :disabled="isLoading" type="url" />
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BaseImageUpload } from '@/components/tags/base';
import { BaseForm } from '@/components/tags/form';
import { publisherRules } from '@/components/tags/form/rules';
import type { BookPublisher, UploadBookPublisher } from '@/composables';
import { ApiUrls, useCreate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElForm, ElFormItem, ElInput } from 'element-plus';
import { reactive, ref, watch } from 'vue';

export interface Props {
  modelValue: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', entity: UploadBookPublisher): void;
}>();

const isLoading = ref(false);

const formRef = ref<FormInstance>();
const form = reactive<UploadBookPublisher>({
  image: undefined,
  name: undefined,
  email: undefined,
  address: undefined,
  website: undefined,
});

function resetForm() {
  form.image = undefined;
  form.name = undefined;
  form.email = undefined;
  form.address = undefined;
  form.website = undefined;
}

watch(
  () => props.modelValue,
  async after => {
    if (after) {
      resetForm();
    }
  },
);

async function sendData() {
  isLoading.value = true;

  const updateData: UploadBookPublisher = { ...form };

  if (!form.image) {
    updateData.removeImage = true;
  } else {
    updateData.image = form.image.raw;
  }

  const success = await useCreate<BookPublisher, UploadBookPublisher>(
    ApiUrls.publishers,
    updateData,
    updateData.image !== undefined,
  );

  if (success) {
    popup('success', 'Publisher Created Successfully!');
    emit('submit', form);
  } else {
    popup('error', 'An error occurred during the Publisher creation!');
  }

  isLoading.value = false;
}

function submitForm() {
  if (!formRef.value) {
    return;
  }

  formRef.value.validate(async isValid => {
    if (isValid) {
      await sendData();
      return true;
    }

    return false;
  });
}
</script>

<style scoped>
.input {
  width: 100%;
}
</style>
