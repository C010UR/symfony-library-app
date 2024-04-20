<template>
  <base-form
    type="create"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <el-form ref="formRef" :model="form" :rules="tagRules" label-position="top">
      <el-form-item label="Name" prop="name">
        <el-input v-model="form.name" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BaseForm } from '@/components/tags/form';
import { tagRules } from '@/components/tags/form/rules';
import type { BookTag, UploadBookTag } from '@/composables';
import { ApiUrls, useCreate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElForm, ElFormItem, ElInput } from 'element-plus';
import { reactive, ref, watch } from 'vue';

export interface Props {
  modelValue: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', entity: UploadBookTag): void;
}>();

const isLoading = ref(false);

const formRef = ref<FormInstance>();
const form = reactive<UploadBookTag>({
  name: undefined,
});

function resetForm() {
  form.name = undefined;
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

  const updateData: UploadBookTag = { ...form };

  const success = await useCreate<BookTag, UploadBookTag>(ApiUrls.tags, updateData, false);

  if (success) {
    popup('success', 'Genre created successfully!');
    emit('submit', form);
  } else {
    popup('error', 'An error occurred during the Genre creation!');
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
