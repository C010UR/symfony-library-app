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
import { Moon, Sunny } from '@element-plus/icons-vue';
import { useDark } from '@vueuse/core';
import { ElSwitch } from 'element-plus';

const isDark = useDark();
</script>
