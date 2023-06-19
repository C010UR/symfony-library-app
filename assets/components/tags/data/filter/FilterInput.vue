<template>
  <div>
    <template v-if="column.type === 'boolean'">
      <template v-if="operator === undefined"></template>
      <template v-else-if="operator === 'eq'">
        <el-switch
          :model-value="(modelValue as boolean)"
          @update:model-value="handleUpdate"
          :active-text="column.data.bool.true"
          :inactive-text="column.data.bool.false"
        />
      </template>
      <template v-else-if="operator === 'null'">
        <el-switch
          :model-value="(modelValue as boolean)"
          @update:model-value="handleUpdate"
          :active-text="column.data?.null?.true"
          :inactive-text="column.data?.null?.false"
        />
      </template>
      <template v-else> Оператор '{{ operator }}' для '{{ column.name }}' не реализован. </template>
    </template>
    <template v-else-if="column.type === 'integer' || column.type === 'float'">
      <template v-if="operator === undefined"></template>
      <template v-else-if="['eq', 'neq', 'gt', 'lt', 'gte', 'lte'].includes(operator)">
        <el-input-number
          :model-value="(modelValue as number)"
          @update:model-value="handleUpdate"
          :precision="column.type === 'float' ? 2 : undefined"
        />
      </template>
      <template v-else-if="['in', 'not-in'].includes(operator)">
        <el-select
          :model-value="(modelValue as number)"
          @update:model-value="handleUpdate"
          multiple
          filterable
          allow-create
          collapse-tags
          collapse-tags-tooltip
        />
      </template>
      <template v-else-if="operator === 'between'">
        <el-input-number
          :model-value="Array.isArray(modelValue) ? (modelValue[0] as number) : undefined"
          @update:model-value="handleUpdateBetweenFirst"
          :precision="column.type === 'float' ? 2 : undefined"
        />
        <span style="margin: 0 1rem">и</span>
        <el-input-number
          :model-value="Array.isArray(modelValue) ? (modelValue[1] as number) : undefined"
          @update:model-value="handleUpdateBetweenSecond"
          :precision="column.type === 'float' ? 2 : undefined"
        />
      </template>
      <template v-else-if="operator === 'null'">
        <el-switch
          :model-value="(modelValue as boolean)"
          @update:model-value="handleUpdate"
          :active-text="column.data?.null?.true"
          :inactive-text="column.data?.null?.false"
        />
      </template>
      <template v-else> Оператор '{{ operator }}' для '{{ column.name }}' не реализован. </template>
    </template>
    <template v-else-if="column.type === 'string'">
      <template v-if="operator === undefined"></template>
      <template v-else-if="['eq', 'neq', 'contains', 'starts-with', 'ends-with'].includes(operator)">
        <el-input :model-value="(modelValue as string)" @update:model-value="handleUpdate" />
      </template>
      <template v-else-if="['in', 'not-in'].includes(operator)">
        <el-select
          :model-value="(modelValue as string)"
          @update:model-value="handleUpdate"
          multiple
          filterable
          allow-create
          collapse-tags
          collapse-tags-tooltip
        />
      </template>
      <template v-else-if="operator === 'null'">
        <el-switch
          :model-value="(modelValue as boolean)"
          @update:model-value="handleUpdate"
          :active-text="column.data?.null?.true"
          :inactive-text="column.data?.null?.false"
        />
      </template>
      <template v-else> Оператор '{{ operator }}' для '{{ column.name }}' не реализован. </template>
    </template>
    <template v-else-if="column.type === 'date'">
      <template v-if="operator === undefined"></template>
      <template v-else-if="['eq', 'gte', 'lte', 'neq'].includes(operator)">
        <el-date-picker :model-value="(modelValue as Date)" @update:model-value="handleUpdate" type="date" />
      </template>
      <template v-else-if="operator === 'between'">
        <el-date-picker
          :model-value="(modelValue as [Date, Date])"
          @update:model-value="handleUpdate"
          type="daterange"
          range-separator="До"
        />
      </template>
      <template v-else-if="operator === 'null'">
        <el-switch
          :model-value="(modelValue as boolean)"
          @update:model-value="handleUpdate"
          :active-text="column.data?.null?.true"
          :inactive-text="column.data?.null?.false"
        />
      </template>
      <template v-else> Оператор '{{ operator }}' для '{{ column.name }}' не реализован. </template>
    </template>
    <template v-else-if="column.type === 'entity' || column.type === 'entities'">
      <template v-if="operator === undefined"></template>
      <template v-else-if="['eq', 'neq', 'in', 'not-in'].includes(operator)">
        <template v-if="column.data.entity === 'author'">
          <el-select
            :model-value="(modelValue as number)"
            @update:model-value="handleUpdate"
            filterable
            :multiple="['in', 'not-in'].includes(operator)"
            :loading="authors === undefined"
            collapse-tags
            collapse-tags-tooltip
          >
            <author-option v-for="author in authors" :key="author.id" :value="author" />
          </el-select>
        </template>
        <template v-else-if="column.data.entity === 'tag'">
          <el-select
            :model-value="(modelValue as number)"
            @update:model-value="handleUpdate"
            filterable
            :multiple="['in', 'not-in'].includes(operator)"
            :loading="tags === undefined"
            collapse-tags
            collapse-tags-tooltip
          >
            <tag-option v-for="tag in tags" :key="tag.id" :value="tag" />
          </el-select>
        </template>
        <template v-else-if="column.data.entity === 'book'">
          <el-select
            :model-value="(modelValue as number)"
            @update:model-value="handleUpdate"
            filterable
            :multiple="['in', 'not-in'].includes(operator)"
            :loading="books === undefined"
            collapse-tags
            collapse-tags-tooltip
          >
            <book-option v-for="book in books" :key="book.id" :value="book" />
          </el-select>
        </template>
        <template v-else-if="column.data.entity === 'publisher'">
          <el-select
            :model-value="(modelValue as number)"
            @update:model-value="handleUpdate"
            filterable
            :multiple="['in', 'not-in'].includes(operator)"
            :loading="publishers === undefined"
            collapse-tags
            collapse-tags-tooltip
          >
            <publisher-option v-for="publisher in publishers" :key="publisher.id" :value="publisher" />
          </el-select>
        </template>
        <template v-else-if="column.data.entity === 'user'">
          <el-select
            :model-value="(modelValue as number)"
            @update:model-value="handleUpdate"
            filterable
            :multiple="['in', 'not-in'].includes(operator)"
            :loading="publishers === undefined"
            collapse-tags
            collapse-tags-tooltip
          >
            <user-option v-for="user in users" :key="user.id" :value="user" />
          </el-select>
        </template>
        <template v-else> Тип поля '{{ column.data.entity }}' не реализован. </template>
      </template>
      <template v-else-if="operator === 'null'">
        <el-switch
          :model-value="(modelValue as boolean)"
          @update:model-value="handleUpdate"
          :active-text="column.data?.null?.true"
          :inactive-text="column.data?.null?.false"
        />
      </template>
      <template v-else> Оператор '{{ operator }}' для '{{ column.name }}' не реализован. </template>
    </template>
    <template v-else> Поле '{{ column.name }}' не реализовано. </template>
  </div>
</template>

<script async setup lang="ts">
import { AuthorOption, BookOption, PublisherOption, TagOption, UserOption } from '@/components/tags/entity-option';
import type {
  Book,
  BookAuthor,
  BookPublisher,
  BookTag,
  FilterOperator,
  FilterOption,
  UserProfile,
} from '@/composables';
import { ApiUrls, useGetAll } from '@/composables';
import { ElDatePicker, ElInput, ElInputNumber, ElSelect, ElSwitch } from 'element-plus';
import { ref, watch, watchEffect } from 'vue';

export interface Props {
  column: FilterOption;
  operator?: FilterOperator;
  modelValue: unknown | [unknown, unknown];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: unknown | [unknown, unknown]): void;
}>();

watch(
  () => props.operator,
  (after, before) => {
    if (after !== before) {
      emit('update:modelValue', undefined);
    }
  },
);

const authors = ref<BookAuthor[] | undefined>();
const tags = ref<BookTag[] | undefined>();
const books = ref<Book[] | undefined>();
const publishers = ref<BookPublisher[] | undefined>();
const users = ref<UserProfile[] | undefined>();

watchEffect(async () => {
  if (props.column.type !== 'entity' && props.column.type !== 'entities') {
    return;
  }

  switch (props.column.data.entity) {
    case 'author': {
      if (authors.value === undefined || authors.value.length === 0) {
        authors.value = (await useGetAll<BookAuthor>(ApiUrls.authors))?.data;
      }
      break;
    }
    case 'book': {
      if (books.value === undefined || books.value.length === 0) {
        books.value = (await useGetAll<Book>(ApiUrls.books))?.data;
      }
      break;
    }
    case 'publisher': {
      if (publishers.value === undefined || publishers.value.length === 0) {
        publishers.value = (await useGetAll<BookPublisher>(ApiUrls.publishers))?.data;
      }
      break;
    }
    case 'tag': {
      if (tags.value === undefined || tags.value.length === 0) {
        tags.value = (await useGetAll<BookTag>(ApiUrls.tags))?.data;
      }
      break;
    }
    case 'user': {
      if (users.value === undefined || users.value.length === 0) {
        users.value = (await useGetAll<UserProfile>(ApiUrls.users))?.data;
      }
      break;
    }
    default: {
      const _exhaustiveCheck: never = props.column.data.entity;
      return _exhaustiveCheck;
    }
  }
});

function handleUpdate(value: unknown) {
  emit('update:modelValue', value);
}

function handleUpdateBetweenFirst(value: unknown) {
  emit('update:modelValue', [value, Array.isArray(props.modelValue) ? props.modelValue[1] : undefined]);
}

function handleUpdateBetweenSecond(value: unknown) {
  emit('update:modelValue', [Array.isArray(props.modelValue) ? props.modelValue[0] : undefined, value]);
}
</script>
