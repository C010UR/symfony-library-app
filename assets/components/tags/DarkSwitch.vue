<template>
  <el-switch
    size="large"
    v-model="isDark"
    inline-prompt
    active-text="Dark"
    inactive-text="Light"
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
