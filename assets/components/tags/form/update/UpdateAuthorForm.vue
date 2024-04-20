<template>
  <base-form
    v-if="entity"
    type="update"
    :id="entity.id"
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
      <el-form-item label="Surname" prop="lastName">
        <el-input v-model="form.lastName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Middle Name" prop="middleName">
        <el-input v-model="form.middleName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Email" prop="email">
        <el-input v-model="form.email" maxlength="255" show-word-limit clearable :disabled="isLoading" type="email" />
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
import { authorRules } from '@/components/tags/form/rules';
import type { BookAuthor, UploadBookAuthor } from '@/composables';
import { ApiUrls, useUpdate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElForm, ElFormItem, ElInput } from 'element-plus';
import { reactive, ref, watch } from 'vue';

export interface Props {
  modelValue: boolean;
  entity: BookAuthor | undefined;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', entity: UploadBookAuthor): void;
}>();

const isLoading = ref(false);

const formRef = ref<FormInstance>();
const form = reactive<UploadBookAuthor>({
  image: undefined,
  firstName: undefined,
  lastName: undefined,
  middleName: undefined,
  email: undefined,
  website: undefined,
});

function resetForm() {
  form.firstName = props.entity?.firstName;
  form.lastName = props.entity?.lastName;
  form.middleName = props.entity?.middleName;
  form.email = props.entity?.email;
  form.website = props.entity?.website;

  if (props.entity?.image) {
    form.image = {
      name: props.entity.image.substring(props.entity.image.lastIndexOf('/') + 1),
      url: props.entity.image,
    };
  } else {
    form.image = undefined;
  }
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

  if (form.firstName !== props.entity?.firstName) {
    updateData.firstName = form.firstName;
  }

  if (form.lastName !== props.entity?.lastName) {
    updateData.lastName = form.lastName;
  }

  if (form.middleName !== props.entity?.middleName) {
    updateData.middleName = form.middleName;
  }

  if (!form.image) {
    updateData.removeImage = true;
  } else {
    updateData.image = form.image.raw;
  }

  if (form.email !== props.entity?.email) {
    updateData.email = form.email;
  }

  if (form.website !== props.entity?.website) {
    updateData.website = form.website;
  }

  const success = await useUpdate<BookAuthor, UploadBookAuthor>(
    ApiUrls.authors,
    props.entity?.id ? props.entity.id : -1,
    updateData,
    updateData.image !== undefined,
  );

  if (success) {
    popup('success', 'Author updated!');
    emit('submit', form);
  } else {
    popup('error', 'An error occurred during the Author update!');
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
