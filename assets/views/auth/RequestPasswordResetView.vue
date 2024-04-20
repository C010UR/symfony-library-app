<template>
  <base-card-page header="Change password">
    <el-form ref="formRef" class="form" label-position="top" :model="form" :rules="requestPasswordResetRules">
      <p>
        Enter the email address you used for this site and we will send pam an email with instructions on how to reset your password.
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
            Go back
          </el-link>
          <el-button type="primary" :loading="disabled" @click="submitForm()"> Send Email </el-button>
        </div>
      </el-form-item>
    </el-form>
  </base-card-page>
</template>

<script setup lang="ts">
import { BaseCardPage } from '@/components/pages';
import { requestPasswordResetRules } from '@/components/tags/form/rules';
import { useRequestPasswordReset } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElButton, ElForm, ElFormItem, ElInput, ElLink } from 'element-plus';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

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
</script>
