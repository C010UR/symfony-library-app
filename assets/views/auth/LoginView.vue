<template>
  <base-card-page header="Авторизация">
    <el-form ref="formRef" class="form" label-position="top" :model="form" :rules="loginRules">
      <el-form-item label="Email" prop="email">
        <el-input
          v-model="form.email"
          :disabled="disabled"
          maxlength="254"
          show-word-limit
          clearable
          style="max-width: 132ch"
        ></el-input>
      </el-form-item>
      <el-form-item label="Пароль" prop="password">
        <el-input
          v-model="form.password"
          v-on:keyup.enter="submitForm()"
          :disabled="disabled"
          maxlength="32"
          type="password"
          show-password
          style="max-width: 132ch"
        ></el-input>
      </el-form-item>
      <el-form-item>
        <div style="margin-left: auto; margin-right: 0; margin-top: 1rem">
          <el-link type="primary" :underline="false" style="margin-right: 1rem" @click="$router.push({ name: 'Main' })">
            На Главную
          </el-link>
          <el-link
            type="primary"
            :underline="false"
            style="margin-right: 1rem"
            @click="$router.push({ name: 'ResetPasswordRequest' })"
          >
            Забыли пароль?
          </el-link>
          <el-button type="primary" :loading="disabled" @click="submitForm()"> Войти </el-button>
        </div>
      </el-form-item>
    </el-form>
  </base-card-page>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElForm, ElFormItem, ElInput, ElButton, ElLink } from 'element-plus';
import type { FormInstance } from 'element-plus';
import { BaseCardPage } from '@/components/pages';
import { useLogin } from '@/composables';
import { loginRules } from '@/components/tags/form/rules';

const formRef = ref<FormInstance>();
const disabled = ref(false);
const router = useRouter();

const form = ref({
  email: '',
  password: '',
});

async function fetch() {
  const data = await useLogin(form.value.email, form.value.password);

  if (data === undefined) {
    form.value.password = '';
    disabled.value = false;
  } else {
    popup('success', `Успешно авторизованы как ${data.fullName}`);
    router.push({ name: 'Main' });
  }
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
</script>
