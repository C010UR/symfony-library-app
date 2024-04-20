<template>
  <base-card-page :header="path">
    <el-empty description="Page Not Found!" />
    <el-button type="primary" @click="$router.push({ name: 'Main' })" style="float: right; margin-bottom: 2.31rem">
      Go Back to The Main Page
    </el-button>
  </base-card-page>
</template>

<script setup lang="ts">
import { BaseCardPage } from '@/components/pages';
import { ElButton, ElEmpty } from 'element-plus';
import { useRoute } from 'vue-router';

const route = useRoute();

let path = route.path.slice(-15);

if (route.fullPath.length > 15) {
  path = '/...' + path;
}
</script>
