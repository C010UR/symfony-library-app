<template>
  <base-form
    type="create"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <el-form ref="formRef" :model="form" :rules="userRoles" label-position="top">
      <el-form-item label="Изображение" prop="image">
        <base-image-upload :isLoading="isLoading" v-model="form.image" />
      </el-form-item>
      <el-form-item label="Имя" prop="firstName">
        <el-input v-model="form.firstName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Фамилия" prop="lastName">
        <el-input v-model="form.lastName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Отчество" prop="middleName">
        <el-input v-model="form.middleName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Email" prop="email">
        <el-input v-model="form.email" maxlength="255" show-word-limit clearable :disabled="isLoading" type="email" />
      </el-form-item>
      <el-form-item label="Роли" prop="roles">
        <el-checkbox-group v-model="form.roles" :min="1">
          <el-checkbox label="ROLE_ADMIN" border />
          <el-checkbox label="ROLE_USER" border />
        </el-checkbox-group>
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { BaseImageUpload } from '@/components/tags/base';
import { ref, reactive, watch } from 'vue';
import { ElForm, ElFormItem, ElCheckbox, ElCheckboxGroup, ElInput } from 'element-plus';
import { ApiUrls, useCreate } from '@/composables';
import { popup } from '@/components/tags';
import { BaseForm } from '@/components/tags/form';
import type { UserProfile, UploadUserProfile } from '@/composables';
import type { FormInstance } from 'element-plus';
import { userRoles } from '@/components/tags/form/rules';

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
const form = reactive<UploadUserProfile>({
  image: undefined,
  firstName: undefined,
  lastName: undefined,
  middleName: undefined,
  email: undefined,
  roles: [],
});

function resetForm() {
  form.image = undefined;
  form.firstName = undefined;
  form.lastName = undefined;
  form.middleName = undefined;
  form.email = undefined;
  form.roles = [];
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

  const updateData: UploadUserProfile = { ...form };

  if (!form.image) {
    updateData.removeImage = true;
  } else {
    updateData.image = form.image.raw;
  }

  const success = await useCreate<UserProfile, UploadUserProfile>(
    ApiUrls.users,
    updateData,
    updateData.image !== undefined,
  );

  if (success) {
    popup('success', 'Пользователь успешно создан! Пользователь нужно сбросить пароль, чтобы активировать аккаунт.');
    emit('submit', form);
  } else {
    popup('error', 'Не удалось создать пользователя!');
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
