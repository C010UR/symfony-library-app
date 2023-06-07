<template>
  <base-card-page header="Письмо отправлено!">
    <p>Следуйте инструкциям, вложенным в отправленное на вашу письмо, чтобы сбросить пароль.</p>
    <p>Если вы не получили письмо в течение 5-ти минут, проверьте спам или обратитесь к администратору.</p>

    <el-button
      style="float: right; margin-bottom: 2.31rem; margin-top: 1rem"
      type="primary"
      @click="$router.push({ name: 'Login' })"
    >
      Войти
    </el-button>
  </base-card-page>
</template>

<script setup lang="ts">
import { ElButton } from 'element-plus';
import { BaseCardPage } from '@/components/pages';
</script>
