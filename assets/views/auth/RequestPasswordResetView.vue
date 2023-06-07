<template>
  <base-card-page header="Сбросить пароль">
    <el-form ref="formRef" class="form" label-position="top" :model="form" :rules="rules">
      <p>
        Введите адрес электронной почты, которую вы использовали для данного сайта и мы отправим пам письмо с
        инструкциями по сбросу пароля.
      </p>
      <el-form-item label="Email" prop="email">
        <el-input
          v-model="form.email"
          v-on:keyup.enter="submitForm()"
          :disabled="disabled"
          maxlength="254"
          show-word-limit
          clearable
          style="max-width: 132ch"
        ></el-input>
      </el-form-item>
      <el-form-item>
        <div style="margin-left: auto; margin-right: 0; margin-top: 1rem">
          <el-link
            type="primary"
            :underline="false"
            style="margin-right: 1rem"
            @click="$router.push({ name: 'Login' })"
          >
            Вернуться
          </el-link>
          <el-button type="primary" :loading="disabled" @click="submitForm()"> Отправить письмо </el-button>
        </div>
      </el-form-item>
    </el-form>
  </base-card-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElForm, ElFormItem, ElInput, ElButton, ElLink } from 'element-plus';
import type { FormInstance, FormRules } from 'element-plus';
import { BaseCardPage } from '@/components/pages';
import { useRequestPasswordReset } from '@/composables';

const router = useRouter();

const formRef = ref<FormInstance>();
const disabled = ref(false);

const form = ref({
  email: '',
});

async function fetch() {
  const url = new URL(
    router.resolve({
      name: 'ResetPasswordRequest',
    }).href,
    window.location.origin,
  ).href;

  const data = await useRequestPasswordReset(form.value.email, url);

  if (data === null) {
    disabled.value = false;
  } else {
    router.push({ name: 'ResetPasswordConfirm' });
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

const rules = ref<FormRules>({
  email: [
    {
      required: true,
      message: 'Email не может быть пустым',
      trigger: 'blur',
    },
    {
      type: 'email',
      message: 'Email не действителен',
      trigger: ['blur', 'change'],
    },
  ],
});
</script>
