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
    <el-form ref="formRef" :model="form" :rules="userRoles" label-position="top">
      <el-form-item label="Profile Image" prop="image">
        <base-image-upload :isLoading="isLoading" v-model="form.image" />
      </el-form-item>
      <el-form-item label="Firsd Name" prop="firstName">
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
      <el-form-item label="Roles" prop="roles">
        <el-checkbox-group v-model="roleFormItem" :min="1">
          <el-checkbox name="ROLE_USER" label="Admin" border />
          <el-checkbox name="ROLE_ADMIN" label="User" border />
        </el-checkbox-group>
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BaseImageUpload } from '@/components/tags/base';
import { BaseForm } from '@/components/tags/form';
import { userRoles } from '@/components/tags/form/rules';
import type { UploadUserProfile, UserProfile } from '@/composables';
import { ApiUrls, useUpdate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElCheckbox, ElCheckboxGroup, ElForm, ElFormItem, ElInput } from 'element-plus';
import lodash from 'lodash';
import { reactive, ref, watch } from 'vue';
import type { UserRoleLabelType } from '../types';
import router from '@/router';

export interface Props {
  modelValue: boolean;
  entity: UserProfile | undefined;
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
  link: new URL(
    router.resolve({
      name: 'ResetPasswordRequest',
    }).href,
    window.location.origin,
  ).href,
});

const roleFormItem = ref<UserRoleLabelType[]>(['User']);

function resetForm() {
  form.firstName = props.entity?.firstName;
  form.lastName = props.entity?.lastName;
  form.middleName = props.entity?.middleName;
  form.email = props.entity?.email;
  form.roles = props.entity?.roles ?? [];

  if (props.entity?.image) {
    form.image = {
      name: props.entity.image.substring(props.entity.image.lastIndexOf('/') + 1),
      url: props.entity.image,
    };
  } else {
    form.image = undefined;
  }

  if (props.entity?.roles) {
    roleFormItem.value = [];

    for (const role of props.entity.roles) {
      switch (role) {
        case 'ROLE_USER': {
          console.log('user');
          roleFormItem.value.push('User');
          break;
        }
        case 'ROLE_ADMIN': {
          console.log('admin');
          roleFormItem.value.push('Admin');
          break;
        }
        default: {
          return role;
        }
      }
    }
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

// transform role radio group to array that backend can understand
watch(roleFormItem, () => {
  form.roles = [];

  for (const role of roleFormItem.value) {
    switch (role) {
      case 'User': {
        form.roles.push('ROLE_USER');
        break;
      }
      case 'Admin': {
        form.roles.push('ROLE_ADMIN');
        break;
      }
      default: {
        return role;
      }
    }
  }
});

async function sendData() {
  isLoading.value = true;

  const updateData: UploadUserProfile = { ...form };

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

  if (lodash.isEqual(form.roles, props.entity?.roles)) {
    updateData.roles = [...form.roles];
  }

  const success = await useUpdate<UserProfile, UploadUserProfile>(
    ApiUrls.users,
    props.entity?.id ? props.entity.id : -1,
    updateData,
    updateData.image !== undefined,
  );

  if (success) {
    popup(
      'success',
      'User updated successfully! User password was reset and password reset email was sent.',
    );
    emit('submit', form);
  } else {
    popup('error', 'An error occurred during the User update!');
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
