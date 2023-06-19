<template>
  <base-form
    type="create"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <book-short-info :book="book" />
    <el-form ref="formRef" :model="form" :rules="orderRules" label-position="top">
      <el-form-item label="Имя" prop="firstName">
        <el-input v-model="form.firstName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Фамилия" prop="lastName">
        <el-input v-model="form.lastName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Отчество" prop="middleName">
        <el-input v-model="form.middleName" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Телефон" prop="phoneNumber">
        <el-input
          v-model="form.phoneNumber"
          clearable
          :disabled="isLoading"
          type="tel"
          :formatter="(value: string) => value.replace(/^\+375|\D/g, '').replace(/^(\d{1,2})(\d{1,3})?(\d{1,2})?(\d{1,2})?.*/, (m, g1, g2, g3, g4) => `(${g1})` + (g2 ? ` ${g2}` : '') + (g3 ? `-${g3}` : '') + (g4 ? `-${g4}` : ''))"
        >
          <template #prepend>+375</template>
        </el-input>
      </el-form-item>
      <el-form-item label="Количество" prop="quantity">
        <el-input-number v-model="form.quantity" :min="0" :disabled="isLoading" />
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BookShortInfo } from '@/components/tags/data';
import { BaseForm } from '@/components/tags/form';
import { orderRules } from '@/components/tags/form/rules';
import type { Book, BookOrder, UploadBookOrder } from '@/composables';
import { ApiUrls, useCreate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElForm, ElFormItem, ElInput, ElInputNumber } from 'element-plus';
import { reactive, ref, watch } from 'vue';

export interface Props {
  modelValue: boolean;
  book: Book | undefined;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', entity: UploadBookOrder): void;
}>();

const isLoading = ref(false);

const formRef = ref<FormInstance>();
const form = reactive<UploadBookOrder>({
  firstName: undefined,
  lastName: undefined,
  middleName: undefined,
  phoneNumber: undefined,
  quantity: 1,
  book: props.book?.id,
});

function resetForm() {
  form.firstName = undefined;
  form.lastName = undefined;
  form.middleName = undefined;
  form.phoneNumber = undefined;
  form.quantity = 1;
  form.book = props.book?.id;
}

watch(
  () => props.modelValue,
  async after => {
    if (after) {
      resetForm();
    }
  },
);

async function sendData() {
  isLoading.value = true;

  const updateData: UploadBookOrder = { ...form };

  const success = await useCreate<BookOrder, UploadBookOrder>(ApiUrls.orders, updateData);

  if (success) {
    popup('success', 'Заказ успешно создан! Ожидайте его в библиотеке.');
    emit('submit', form);
  } else {
    popup('error', 'Не удалось создать заказ!');
  }

  isLoading.value = false;
}

function submitForm() {
  if (!formRef.value) {
    return;
  }

  formRef.value.validate(async isValid => {
    if (isValid) {
      await sendData();
      return true;
    }

    return false;
  });
}
</script>

<style scoped>
.input {
  width: 100%;
}
</style>
