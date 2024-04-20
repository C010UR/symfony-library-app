<template>
  <base-card-page header="Email sent!">
    <p>Follow the instructions attached to the email sent to your email to reset your password.</p>
    <p>If you don't receive an email within 5 minutes, check spam or contact an administrator.</p>

    <el-button
      style="float: right; margin-bottom: 2.31rem; margin-top: 1rem"
      type="primary"
      @click="$router.push({ name: 'Login' })"
    >
      Log in
    </el-button>
  </base-card-page>
</template>

<script setup lang="ts">
import { BaseCardPage } from '@/components/pages';
import { ElButton } from 'element-plus';
</script>
