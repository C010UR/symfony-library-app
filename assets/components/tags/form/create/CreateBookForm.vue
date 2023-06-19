<template>
  <base-form
    type="create"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <el-form ref="formRef" :model="form" :rules="bookRules" label-position="top">
      <el-form-item label="Обложка" prop="image">
        <base-image-upload :isLoading="isLoading" v-model="form.image" />
      </el-form-item>
      <el-form-item label="Название" prop="name">
        <el-input v-model="form.name" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Количество страниц" prop="pageCount">
        <el-input-number v-model="form.pageCount" :min="0" :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Дата публикации" prop="datePublished">
        <el-date-picker v-model="form.datePublished" :disabled="isLoading" format="MMM D, YYYY" />
      </el-form-item>
      <el-form-item label="Издатель" prop="publisher">
        <el-select
          v-model="form.publisher"
          :disabled="isLoading"
          filterable
          :loading="publishers === undefined"
          class="input"
        >
          <publisher-option v-for="publisher in publishers" :key="publisher.id" :value="publisher" />
        </el-select>
      </el-form-item>
      <el-form-item label="Авторы" prop="authors">
        <el-select
          v-model="form.authors"
          :disabled="isLoading"
          filterable
          multiple
          collapse-tags
          collapse-tags-tooltip
          :max-collapse-tags="3"
          :loading="authors === undefined"
          class="input"
        >
          <author-option v-for="author in authors" :key="author.id" :value="author" />
        </el-select>
      </el-form-item>
      <el-form-item label="Жанры" prop="tags">
        <el-select
          v-model="form.tags"
          :disabled="isLoading"
          filterable
          multiple
          collapse-tags
          collapse-tags-tooltip
          :max-collapse-tags="3"
          :loading="tags === undefined"
          class="input"
        >
          <tag-option v-for="tag in tags" :key="tag.id" :value="tag" />
        </el-select>
      </el-form-item>
      <el-form-item label="Описание" prop="description">
        <el-input
          v-model="form.description"
          autosize
          type="textarea"
          clearable
          :disabled="isLoading"
          maxlength="4096"
          show-word-limit
        />
      </el-form-item>
    </el-form>
  </base-form>
</template>

<script setup lang="ts">
import { BaseImageUpload } from '@/components/tags/base';
import { ref, reactive, watch } from 'vue';
import { ElForm, ElFormItem, ElInput, ElSelect, ElDatePicker, ElInputNumber } from 'element-plus';

import { AuthorOption, PublisherOption, TagOption } from '@/components/tags/entity-option';
import { useGetAll, ApiUrls, useCreate } from '@/composables';
import { popup } from '@/components/tags';
import { BaseForm } from '@/components/tags/form';
import type { UploadBook, BookAuthor, BookTag, BookPublisher, Book } from '@/composables';
import type { FormInstance } from 'element-plus';
import { bookRules } from '@/components/tags/form/rules';

export interface Props {
  modelValue: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', entity: UploadBook): void;
}>();

const isLoading = ref(false);

const authors = ref<BookAuthor[] | undefined>();
const tags = ref<BookTag[] | undefined>();
const publishers = ref<BookPublisher[] | undefined>();

const formRef = ref<FormInstance>();
const form = reactive<UploadBook>({
  image: undefined,
  name: undefined,
  pageCount: undefined,
  datePublished: undefined,
  publisher: undefined,
  authors: [],
  tags: [],
  description: undefined,
});

function resetForm() {
  form.image = undefined;
  form.name = undefined;
  form.pageCount = undefined;
  form.datePublished = undefined;
  form.publisher = undefined;
  form.authors = [];
  form.tags = [];
  form.description = undefined;
}

async function getData() {
  if (publishers.value === undefined || publishers.value.length === 0) {
    publishers.value = (await useGetAll<BookPublisher>(ApiUrls.publishers))?.data;
  }

  if (authors.value === undefined || authors.value.length === 0) {
    authors.value = (await useGetAll<BookAuthor>(ApiUrls.authors))?.data;
  }

  if (tags.value === undefined || tags.value.length === 0) {
    tags.value = (await useGetAll<BookTag>(ApiUrls.tags))?.data;
  }
}

watch(
  () => props.modelValue,
  async after => {
    if (after) {
      resetForm();
      await getData();
    }
  },
);

async function sendData() {
  isLoading.value = true;

  const updateData: UploadBook = { ...form };

  if (!form.image) {
    updateData.removeImage = true;
  } else {
    updateData.image = form.image.raw;
  }

  if (form.datePublished && typeof form.datePublished !== 'string') {
    form.datePublished = new Date(form.datePublished.getTime() + 3 * 3600 * 1000); // Add 3 hours because el-date-picker gives wrong values
    updateData.datePublished = form.datePublished.toISOString().split('T')[0];
  } else {
    updateData.datePublished = form.datePublished;
  }

  const success = await useCreate<Book, UploadBook>(ApiUrls.books, updateData, updateData.image !== undefined);

  if (success) {
    popup('success', 'Книга успешно создана!');
    emit('submit', form);
  } else {
    popup('error', 'Не удалось создать книгу!');
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
