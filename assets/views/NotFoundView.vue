<template>
    <base-page :header="path">
        <el-empty description="Страница не была найдена!" />
        <el-button
            type="primary"
            @click="router.push({ name: 'Main' })"
            style="float: right; margin-bottom: 2.31rem"
        >
            Вернуться на Главную страницу
        </el-button>
    </base-page>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';
import { ElEmpty, ElButton } from 'element-plus';
import BasePage from '~/components/pages/BasePage.vue';

const route = useRoute();

let path = route.path.slice(-15);

if (route.fullPath.length > 15) {
    path = '/...' + path;
}
</script>
