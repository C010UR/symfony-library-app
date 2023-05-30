<template>
    <base-page header="Сброс пароля">
        <el-form
            ref="formRef"
            class="form"
            label-position="top"
            :model="form"
            :rules="rules"
        >
            <el-form-item label="Пароль" prop="password">
                <el-input
                    v-model="form.password"
                    v-on:keyup.enter="formRef.password.focus()"
                    :disabled="disabled"
                    maxlength="254"
                    show-word-limit
                    clearable
                    style="max-width: 132ch"
                    type="password"
                    show-password
                ></el-input>
            </el-form-item>
            <el-form-item label="Подтверждение пароля" prop="passwordConfirm">
                <el-input
                    v-model="form.passwordConfirm"
                    v-on:keyup.enter="submitForm()"
                    ref="passwordConfirmInput"
                    :disabled="disabled"
                    maxlength="254"
                    show-word-limit
                    clearable
                    style="max-width: 132ch"
                    type="password"
                    show-password
                ></el-input>
            </el-form-item>
            <el-form-item>
                <div
                    style="margin-left: auto; margin-right: 0; margin-top: 1rem"
                >
                    <el-button
                        type="primary"
                        :loading="disabled"
                        @click="submitForm()"
                    >
                        Сбросить Пароль
                    </el-button>
                </div>
            </el-form-item>
        </el-form>
    </base-page>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElForm, ElFormItem, ElInput, ElButton } from 'element-plus';
import validator from 'validator';
import { BasePage } from '~/components/pages/index.js';
import { useResetPassword } from '~/use/index.js';

const router = useRouter();
const route = useRoute();

const formRef = ref(null);
const disabled = ref(false);

const form = reactive({
    password: '',
    passwordConfirm: '',
});

async function fetch() {
    const path = route.path.split('/');
    const token = path.pop() || path.pop();
    const success = await useResetPassword({ token, password: form.password });

    if (!success) {
        disabled.value = false;
        return;
    }

    router.push({ name: 'Login' });
}

function submitForm() {
    formRef.value.validate(isValid => {
        if (isValid) {
            disabled.value = true;
            fetch();
            return true;
        }

        return false;
    });
}

function validatePass(rule, value, callback) {
    if (!validator.isStrongPassword(form.password, { minSymbols: 0 })) {
        callback(new Error('Пароль недостаточно надежный'));
    }

    if (form.passwordConfirm) {
        formRef.value.validateField('passwordConfirm', () => null);
    }

    callback();
}

function validatePass2(rule, value, callback) {
    if (value !== form.password) {
        callback(new Error('Пароли не совпадают'));
    }

    callback();
}

const rules = reactive({
    password: [
        {
            required: true,
            message: 'Пароль не может быть пустым',
            trigger: 'blur',
        },
        { validator: validatePass, trigger: ['blur', 'change'] },
    ],
    passwordConfirm: [
        {
            required: true,
            message: 'Подтверждение пароля не может быть пустым',
            trigger: 'blur',
        },
        { validator: validatePass2, trigger: ['blur', 'change'] },
    ],
});
</script>
