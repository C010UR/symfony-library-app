<template>
    <base-page header="Авторизация">
        <el-form
            ref="formRef"
            class="form"
            label-position="top"
            :model="form"
            :rules="rules"
        >
            <el-form-item label="Email" prop="email">
                <el-input
                    v-model="form.email"
                    v-on:keyup.enter="passwordInput.focus()"
                    :disabled="disabled"
                    maxlength="254"
                    show-word-limit
                    clearable
                    style="max-width: 132ch"
                ></el-input>
            </el-form-item>
            <el-form-item label="Пароль" prop="password">
                <el-input
                    v-model="form.password"
                    v-on:keyup.enter="submitForm()"
                    ref="passwordInput"
                    :disabled="disabled"
                    maxlength="32"
                    type="password"
                    show-password
                    style="max-width: 132ch"
                ></el-input>
            </el-form-item>
            <el-form-item>
                <div
                    style="margin-left: auto; margin-right: 0; margin-top: 1rem"
                >
                    <el-link
                        type="primary"
                        :underline="false"
                        style="margin-right: 1rem"
                        @click="redirectToForgotPassword()"
                    >
                        Забыли пароль?
                    </el-link>
                    <el-button
                        type="primary"
                        :loading="disabled"
                        @click="submitForm()"
                    >
                        Войти
                    </el-button>
                </div>
            </el-form-item>
        </el-form>
    </base-page>
</template>

<script setup>
import { popup } from '~/components/tags/index.js';
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { ElForm, ElFormItem, ElInput, ElButton, ElLink } from 'element-plus';
import { BasePage } from '~/components/pages/index.js';
import { login } from '~/api/index.js';

const router = useRouter();
const passwordInput = ref(null);

const formRef = ref(null);
const form = reactive({
    email: '',
    password: '',
});

const disabled = ref(false);

async function sendData() {
    const data = await login(form.email, form.password);

    if (!data) {
        form.password = '';
        disabled.value = false;
    } else {
        popup(
            'success',
            `Успешно авторизован как ${data.email} с ролями ${JSON.stringify(
                data.roles,
            )}`,
        );
    }
}

function submitForm() {
    formRef.value.validate(isValid => {
        if (isValid) {
            disabled.value = true;
            sendData();
            return true;
        }

        return false;
    });
}

function redirectToForgotPassword() {
    router.push({ name: 'ResetPasswordRequest' });
}

const rules = reactive({
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
    password: [
        {
            required: true,
            message: 'Пароль не может быть пустым',
            trigger: 'blur',
        },
    ],
});
</script>
