<template>
    <base-page header="Письмо отправлено!">
        <p>
            Следуйте инструкциям, вложенным в отправленное на вашу письмо, чтобы
            сбросить пароль.
        </p>
        <p>
            Если вы не получили письмо в течение 5-ти минут, проверьте спам или
            обратитесь к администратору.
        </p>

        <el-button
            style="float: right; margin-bottom: 2.31rem; margin-top: 1rem"
            type="primary"
            @click="redirectToLoginPage()"
        >
            Войти
        </el-button>
    </base-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { ElButton } from 'element-plus';
import { BasePage } from '~/components/pages/index.js';

const router = useRouter();

function redirectToLoginPage() {
    router.push({ name: 'Login' });
}
</script>
