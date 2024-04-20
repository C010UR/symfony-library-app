<template>
  <base-form
    type="create"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <el-form ref="formRef" :model="form" :rules="authorRules" label-position="top">
      <el-form-item label="Profile Image" prop="image">
        <base-image-upload :isLoading="isLoading" v-model="form.image" />
      </el-form-item>
      <el-form-item label="First Name" prop="firstName">
        <el-input v-model="form.firstName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Last Name" prop="lastName">
        <el-input v-model="form.lastName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Middle Name" prop="middleName">
        <el-input v-model="form.middleName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Email" prop="email">
        <el-input v-model="form.email" maxlength="255" show-word-limit clearable :disabled="isLoading" type="email" />
      </el-form-item>
      <el-form-item label="Landing Page" prop="website">
        <el-input v-model="form.website" maxlength="255" show-word-limit clearable :disable="isLoading" type="url" />
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BaseImageUpload } from '@/components/tags/base';
import { BaseForm } from '@/components/tags/form';
import { authorRules } from '@/components/tags/form/rules';
import type { BookAuthor, UploadBookAuthor, UploadUserProfile, UserProfile } from '@/composables';
import { ApiUrls, useCreate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElCheckbox, ElCheckboxGroup, ElForm, ElFormItem, ElInput } from 'element-plus';
import { reactive, ref, watch } from 'vue';

export interface Props {
  modelValue: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', entity: UploadUserProfile): void;
}>();

const isLoading = ref(false);

const formRef = ref<FormInstance>();
const form = reactive<UploadBookAuthor>({
  image: undefined,
  firstName: undefined,
  lastName: undefined,
  middleName: undefined,
  email: undefined,
  website: '',
});

function resetForm() {
  form.image = undefined;
  form.firstName = undefined;
  form.lastName = undefined;
  form.middleName = undefined;
  form.email = undefined;
  form.website = '';
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

  const updateData: UploadBookAuthor = { ...form };

  if (!form.image) {
    updateData.removeImage = true;
  } else {
    updateData.image = form.image.raw;
  }

  const success = await useCreate<BookAuthor, UploadBookAuthor>(
    ApiUrls.authors,
    updateData,
    updateData.image !== undefined,
  );

  if (success) {
    popup(
      'success',
      'Author created successfully.',
    );
    emit('submit', form);
  } else {
    popup('error', 'An error occurred during the Author creation!');
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
