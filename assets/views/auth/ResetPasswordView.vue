<template>
  <base-card-page header="Сброс пароля">
    <el-form ref="formRef" class="form" label-position="top" :model="form" :rules="resetPasswordRules">
      <el-form-item label="Пароль" prop="password">
        <el-input
          v-model="form.password"
          :disabled="disabled"
          maxlength="254"
          show-word-limit
          clearable
          style="max-width: 132ch"
          type="password"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item label="Подтверждение пароля" prop="passwordConfirm">
        <el-input
          v-model="form.passwordConfirm"
          v-on:keyup.enter="submitForm()"
          ref="passwordConfirmInput"
          :disabled="disabled"
          maxlength="254"
          show-word-limit
          clearable
          style="max-width: 132ch"
          type="password"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item>
        <div style="margin-left: auto; margin-right: 0; margin-top: 1rem">
          <el-button type="primary" :loading="disabled" @click="submitForm()"> Сбросить Пароль </el-button>
        </div>
      </el-form-item>
    </el-form>
  </base-card-page>
</template>

<script setup lang="ts">
import { BaseCardPage } from '@/components/pages';
import { useResetPassword } from '@/composables';
import type { FormInstance, FormRules } from 'element-plus';
import { ElButton, ElForm, ElFormItem, ElInput } from 'element-plus';
import validator from 'validator';
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();

const formRef = ref<FormInstance>();
const disabled = ref(false);

const form = ref({
  password: '',
  passwordConfirm: '',
});

async function fetch() {
  const path = route.path.split('/');
  const token = (path.pop() || path.pop()) ?? '';
  const data = await useResetPassword(token, form.value.password);

  if (data === null) {
    disabled.value = false;
    return;
  }

  router.push({ name: 'Login' });
}

function submitForm() {
  formRef.value?.validate(isValid => {
    if (isValid) {
      disabled.value = true;
      fetch();
      return true;
    }

    return false;
  });
}

const passwordsDontMatchMessage = 'Пароли не совпадают';
const passwordInsecureMessage = 'Пароль недостаточно надежный';
const emptyMessage = 'Поле не должно быть пустым';

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function validatePass(rule: any, value: any, callback: any) {
  if (!validator.isStrongPassword(form.value.password, { minSymbols: 0 })) {
    callback(new Error(passwordInsecureMessage));
  }

  if (form.value.passwordConfirm) {
    formRef.value?.validateField('passwordConfirm', () => null);
  }

  callback();
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function validatePass2(rule: any, value: any, callback: any) {
  if (value !== form.value.password) {
    callback(new Error(passwordsDontMatchMessage));
  }

  callback();
}

const resetPasswordRules = reactive<FormRules>({
  password: [
    {
      required: true,
      message: emptyMessage,
      trigger: 'blur',
    },
    { validator: validatePass, trigger: ['blur', 'change'] },
  ],
  passwordConfirm: [
    {
      required: true,
      message: emptyMessage,
      trigger: 'blur',
    },
    { validator: validatePass2, trigger: ['blur', 'change'] },
  ],
});
</script>
