<template>
  <base-form
    v-if="entity"
    type="update"
    :id="entity.id"
    :is-loading="isLoading"
    @submit="submitForm"
    :model-value="modelValue"
    @update:model-value="(emit: boolean) => $emit('update:modelValue', emit)"
  >
    <el-form ref="formRef" :model="form" :rules="bookRules" label-position="top">
      <el-form-item label="Cover" prop="image">
        <base-image-upload :isLoading="isLoading" v-model="form.image" />
      </el-form-item>
      <el-form-item label="Name" prop="name">
        <el-input v-model="form.name" maxlength="255" show-word-limit clearable :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Number of Pages" prop="pageCount">
        <el-input-number v-model="form.pageCount" :min="0" :disabled="isLoading" />
      </el-form-item>
      <el-form-item label="Publication Date" prop="datePublished">
        <el-date-picker v-model="form.datePublished" :disabled="isLoading" format="MMM D, YYYY" />
      </el-form-item>
      <el-form-item label="Publisher" prop="publisher">
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
      <el-form-item label="Authors" prop="authors">
        <el-select
          v-model="form.authors"
          :disabled="isLoading"
          filterable
          multiple
          :loading="authors === undefined"
          class="input"
        >
          <author-option v-for="author in authors" :key="author.id" :value="author" />
        </el-select>
      </el-form-item>
      <el-form-item label="Genres" prop="tags">
        <el-select
          v-model="form.tags"
          :disabled="isLoading"
          filterable
          multiple
          :loading="tags === undefined"
          class="input"
        >
          <tag-option v-for="tag in tags" :key="tag.id" :value="tag" />
        </el-select>
      </el-form-item>
      <el-form-item label="Description" prop="description">
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
import { popup } from '@/components/tags';
import { BaseImageUpload } from '@/components/tags/base';
import { AuthorOption, PublisherOption, TagOption } from '@/components/tags/entity-option';
import { BaseForm } from '@/components/tags/form';
import { bookRules } from '@/components/tags/form/rules';
import type { Book, BookAuthor, BookPublisher, BookTag, UploadBook } from '@/composables';
import { ApiUrls, useGetAll, useUpdate } from '@/composables';
import type { FormInstance } from 'element-plus';
import { ElDatePicker, ElForm, ElFormItem, ElInput, ElInputNumber, ElSelect } from 'element-plus';
import lodash from 'lodash';
import { reactive, ref, watch } from 'vue';

export interface Props {
  modelValue: boolean;
  entity: Book | undefined;
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
  form.name = props.entity?.name;
  form.pageCount = props.entity?.pageCount;
  form.datePublished = new Date(props.entity?.datePublished ?? '');
  form.publisher = props.entity?.publisher.id;
  form.description = props.entity?.description;

  form.authors = [];

  if (props.entity?.authors) {
    for (const author of props.entity.authors) {
      form.authors.push(author.id);
    }
  }

  form.tags = [];

  if (props.entity?.tags) {
    for (const tag of props.entity.tags) {
      form.tags.push(tag.id);
    }
  }

  if (props.entity?.image) {
    form.image = {
      name: props.entity.image.substring(props.entity.image.lastIndexOf('/') + 1),
      url: props.entity.image,
    };
  } else {
    form.image = undefined;
  }
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

  const updateData: UploadBook = {
    authors: [],
    tags: [],
  };

  if (form.name !== props.entity?.name) {
    updateData.name = form.name;
  }

  if (!form.image) {
    updateData.removeImage = true;
  } else if (form.image.url !== props.entity?.image) {
    updateData.image = form.image.raw;
  }

  if (form.pageCount !== props.entity?.pageCount) {
    updateData.pageCount = form.pageCount;
  }

  let datePublished: string | undefined;

  if (form.datePublished && typeof form.datePublished !== 'string') {
    form.datePublished = new Date(form.datePublished.getTime() + 3 * 3600 * 1000); // Add 3 hours because el-date-picker gives wrong values
    datePublished = form.datePublished.toISOString().split('T')[0];
  } else {
    datePublished = form.datePublished;
  }

  if (datePublished !== props.entity?.datePublished) {
    updateData.datePublished = datePublished;
  }

  if (form.publisher !== props.entity?.publisher) {
    updateData.publisher = form.publisher;
  }

  if (!lodash.isEqual(form.authors, props.entity?.authors)) {
    updateData.authors = [...form.authors];
  }

  if (!lodash.isEqual(form.tags, props.entity?.tags)) {
    updateData.tags = [...form.tags];
  }

  if (form.description !== props.entity?.description) {
    updateData.description = form.description;
  }

  const success = await useUpdate<Book, UploadBook>(
    ApiUrls.books,
    props.entity?.id ? props.entity.id : -1,
    updateData,
    updateData.image !== undefined,
  );

  if (success) {
    popup('success', 'Book updated successfully!');
    emit('submit', form);
  } else {
    popup('error', 'An error occurred during the Book update!');
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
