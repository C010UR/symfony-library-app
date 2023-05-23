<template>
    <base-page header="Сбросить пароль">
        <el-form
            ref="formRef"
            class="form"
            label-position="top"
            :model="form"
            :rules="rules"
        >
            <p>
                Введите адрес электронной почты, которую вы использовали для
                данного сайта и мы отправим пам письмо с инструкциями по сбросу
                пароля.
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
                <div
                    style="margin-left: auto; margin-right: 0; margin-top: 1rem"
                >
                    <el-link
                        type="primary"
                        :underline="false"
                        style="margin-right: 1rem"
                        @click="redirectToLoginPage()"
                    >
                        Вернуться
                    </el-link>
                    <el-button
                        type="primary"
                        :loading="disabled"
                        @click="submitForm()"
                    >
                        Отправить письмо
                    </el-button>
                </div>
            </el-form-item>
        </el-form>
    </base-page>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { ElForm, ElFormItem, ElInput, ElButton, ElLink } from 'element-plus';
import { BasePage } from '~/components/pages/index.js';
import { requestResetPassword } from '~/api/index.js';

const router = useRouter();

const formRef = ref(null);
const form = reactive({
    email: '',
});

const disabled = ref(false);

async function sendData() {
    const url = new URL(
        router.resolve({
            name: 'ResetPasswordRequest',
        }).href,
        window.location.origin,
    ).href;

    const success = await requestResetPassword(form.email, url);

    if (success) {
        router.push({ name: 'ResetPasswordConfirm' });
    } else {
        disabled.value = false;
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

function redirectToLoginPage() {
    router.push({ name: 'Login' });
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
});
</script>
