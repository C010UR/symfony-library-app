<template>
  <el-switch
    size="large"
    v-model="isDark"
    inline-prompt
    active-text="Темная тема"
    inactive-text="Светлая тепа"
    :active-icon="Moon"
    :inactive-icon="Sunny"
  />
</template>

<script setup>
import { ElSwitch } from 'element-plus';
import { Sunny, Moon } from '@element-plus/icons-vue';
import { useDark } from '@vueuse/core';

const isDark = useDark();
</script>
