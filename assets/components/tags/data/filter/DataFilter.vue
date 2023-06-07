<template>
  <div class="filter" v-if="filterableColumns.length > 0">
    <div class="filter-header">
      <h1 class="margin-right">Фильтры:</h1>
      <el-switch v-model="toggle" />
    </div>
    <div class="filter-form" v-if="toggle">
      <ul class="filters-list">
        <li v-for="(_filter, index) in modelValue" :key="index">
          <div class="li">
            <el-icon class="margin-right">
              <filter-icon />
            </el-icon>
            <span> {{ columns.find(column => _filter.column === column.name)?.label }}</span>
            <span class="operator">
              {{ columns.find(column => _filter.column === column.name)?.operators[_filter.operator ?? 'eq'].label }}
            </span>
            <span>: {{ convertValue(_filter) }}</span>
            <el-button type="danger" link class="remove-button" @click="handleFilterRemove(index)"> Удалить </el-button>
          </div>
        </li>
      </ul>
      <el-form
        inline
        label-position="top"
        ref="formNewFilterRef"
        :model="formNew"
        :rules="rules"
        class="form"
        v-if="remainingFilterableColumns.length > 0"
      >
        <el-form-item label="Поле" prop="column">
          <el-select v-model="formNew.column">
            <el-option
              v-for="(column, index) in remainingFilterableColumns"
              :key="index"
              :label="column.label"
              :value="column.name"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Оператор" prop="operator">
          <el-select
            v-model="formNew.operator"
            :disabled="!remainingFilterableColumns.find(column => column.name == formNew.column)"
          >
            <el-option
              v-for="operator in filterOption?.operators"
              :key="operator.operator"
              :label="operator.label"
              :value="operator.operator"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Значение" v-if="formNew.operator !== undefined" prop="value">
          <filter-input :column="(filterOption as FilterOption)" v-model="formNew.value" :operator="formNew.operator" />
        </el-form-item>
      </el-form>
      <el-button type="success" @click="handleFilterAdd" v-if="remainingFilterableColumns.length > 0">
        Добавить фильтр
      </el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ElSwitch, ElButton, ElIcon, ElForm, ElFormItem, ElSelect, ElOption } from 'element-plus';
import type { FormInstance, FormRules } from 'element-plus';
import { ref, computed, watchEffect, watch, reactive } from 'vue';
import { Filter as FilterIcon } from '@element-plus/icons-vue';
import FilterInput from './FilterInput.vue';
import type { Filter, FilterOption } from '@/composables';

export interface Props {
  columns: FilterOption[];
  modelValue: Filter[] | undefined;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', filters: Filter[] | undefined): void;
}>();

const toggle = ref<boolean>(false);

watchEffect(() => {
  if (!toggle.value) {
    emit('update:modelValue', undefined);
  }
});

const formNewFilterRef = ref<FormInstance | undefined>();
const formNew = reactive<Filter>({
  column: '',
  operator: undefined,
  value: undefined,
});

watch(props, (before, after) => {
  if (before.columns !== after.columns) {
    formNew.column = '';
    formNew.operator = undefined;
    formNew.value = undefined;
  }
});

watch(
  () => formNew.column,
  (before, after) => {
    if (before !== after) {
      formNew.operator = undefined;
      formNew.value = undefined;
    }
  },
);

const filterableColumns = computed(() => {
  return props.columns.filter(column => Object.keys(column.operators).length > 0);
});

const remainingFilterableColumns = computed(() => {
  let taken: string[];

  if (props.modelValue === undefined) {
    taken = [];
  } else {
    taken = props.modelValue.map(filter => filter.column);
  }

  return filterableColumns.value.filter(column => !taken?.includes(column.name));
});

const filterOption = computed(() => {
  if (!formNew.column) {
    return undefined;
  }
  return props.columns.find(column => column.name === formNew.column);
});

function handleFilterRemove(index: number) {
  const updated = props.modelValue ? [...props.modelValue] : undefined;

  if (updated !== undefined) {
    updated.splice(index, 1);
  }

  emit('update:modelValue', updated);
}

function handleFilterAdd() {
  formNewFilterRef.value?.validate(isValid => {
    if (!isValid) {
      return false;
    }

    emit('update:modelValue', props.modelValue ? [...props.modelValue, { ...formNew }] : [{ ...formNew }]);

    formNew.column = '';
    formNew.operator = undefined;
    formNew.value = undefined;

    return true;
  });
}

function convertValue(filter: Filter) {
  let value: string;

  if (Array.isArray(filter.value)) {
    if (filter.value[0] instanceof Date) {
      value = '[ ' + (filter.value as Date[]).map(value => value.toISOString().split('T')[0]).join(', ') + ' ]';
    } else {
      value = '[ ' + filter.value.join(', ') + ' ]';
    }
  } else if (filter.value instanceof Date) {
    value = filter.value.toISOString().split('T')[0];
  } else {
    value = String(filter.value);
  }

  return value;
}

const rules = ref<FormRules>({
  column: [
    {
      required: true,
      message: 'Поле не может быть пустым',
      trigger: 'blur',
    },
  ],
  operator: [
    {
      required: true,
      message: 'Оператор не может быть пустым',
      trigger: 'blur',
    },
  ],
  value: [
    {
      required: true,
      message: 'Значение не может быть пустым',
      trigger: 'blur',
    },
  ],
});
</script>

<style scoped>
.filter-header {
  display: flex;
  flex-direction: row;
  align-items: baseline;
}

.filter-form {
  width: 80%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.filters-list {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
.li {
  border-radius: var(--el-border-radius-base);
  display: flex;
  flex-direction: row;
  align-items: center;
}

.li:hover {
  background-color: var(--el-bg-color-overlay);
}

.remove-button {
  padding-left: 3rem;
  margin-left: auto;
  margin-right: 0;
}

.margin-right {
  margin-right: 0.5rem;
}

.operator {
  margin: 0 0.4rem;
  font-weight: 600;
}

.form {
  margin-top: 1rem;
}
</style>
