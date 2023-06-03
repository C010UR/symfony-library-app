<template>
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
        >
          <publisher-option v-for="publisher in publishers" :key="publisher.id" :value="publisher" />
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
</template>

<script async setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { ElSwitch, ElInputNumber, ElSelect, ElInput, ElDatePicker } from 'element-plus';
import { AuthorOption, BookOption, TagOption, PublisherOption } from '@/components/tags/entity-option';
import { useGetAll, ApiUrls } from '@/use/api';
import type { BookAuthor, BookFull, BookPublisher, BookTag } from '@/use/api/api';

export interface Props {
  column: FilterOption;
  operator: FilterOperator;
  modelValue: unknown | [unknown, unknown];
}

const props = defineProps<Props>();

const emit = defineEmits(['update:modelValue']);

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
const books = ref<BookFull[] | undefined>();
const publishers = ref<BookPublisher[] | undefined>();

onMounted(async () => {
  authors.value = (await useGetAll<BookAuthor>(ApiUrls.authors))?.data;
  books.value = (await useGetAll<BookFull>(ApiUrls.books))?.data;
  tags.value = (await useGetAll<BookTag>(ApiUrls.tags))?.data;
  publishers.value = (await useGetAll<BookPublisher>(ApiUrls.publishers))?.data;
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
