<template>
  <div class="filter-wrapper" v-if="filterableColumns.length > 0">
    <div class="filter-header">
      <h3>Фильтры:</h3>
    </div>
    <div class="filters">
      <div v-for="column in filterableColumns" :key="column.name" class="filter">
        <p class="filter-column">{{ column.label }}</p>
        <el-select v-model="form[column.name].operator" class="filter-operator">
          <el-option
            v-for="operator in column.operators"
            :key="operator.operator"
            :label="operator.label"
            :value="operator.operator"
          />
        </el-select>
        <filter-input
          v-if="form[column.name].operator !== undefined"
          :column="column"
          v-model="form[column.name].value"
          :operator="form[column.name].operator"
          class="filter-input"
        />
        <el-input v-else disabled class="filter-input" />
        <el-button type="danger" plain :icon="Delete" circle @click="resetFilter(column.name)" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Filter, FilterOption } from '@/composables';
import { Delete } from '@element-plus/icons-vue';
import { watchPausable } from '@vueuse/core';
import { ElButton, ElInput, ElOption, ElSelect } from 'element-plus';
import lodash from 'lodash';
import { computed, reactive } from 'vue';
import FilterInput from './FilterInput.vue';

export interface Props {
  columns: FilterOption[];
  modelValue: Filter[] | undefined;
}

interface Filters {
  [column: string]: Filter;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', filters: Filter[] | undefined): void;
}>();

const form = reactive<Filters>({});

const filterableColumns = computed(() => {
  return props.columns.filter(column => Object.keys(column.operators).length > 0);
});

function resetFilter(name: string) {
  form[name] = {
    column: name,
    operator: undefined,
    value: undefined,
  };
}

const { pause, resume } = watchPausable(form, (after, _, onInvalidate) => {
  console.log('triggered');
  const result: Filter[] = [];

  const updateFilter = setTimeout(() => {
    for (const filter of Object.values(after)) {
      if (filter.operator && filter.value !== undefined) {
        if (
          (Array.isArray(filter.value) && filter.value.length === 0) ||
          (typeof filter.value === 'string' && !filter.value)
        ) {
          continue;
        }

        result.push(filter);
      }
    }

    console.log(props.modelValue, result, result.length === 0);

    if (!lodash.isEqual(props.modelValue, result)) {
      emit('update:modelValue', result.length === 0 ? undefined : result);
    }
  }, 1000);

  onInvalidate(() => {
    clearInterval(updateFilter);
  });
});

function resetForm() {
  const newForm: Filters = {};

  for (const column of filterableColumns.value) {
    newForm[column.name] = {
      column: column.name,
      operator: undefined,
      value: undefined,
    };
  }

  pause();
  Object.assign(form, newForm);
  resume();
}

resetForm();

pause();
if (props.modelValue) {
  for (const filter of Object.values(props.modelValue)) {
    if (!filter.column || !filter.operator) {
      continue;
    }

    form[filter.column] = {
      column: filter.column,
      operator: filter.operator,
      value: filter.value,
    };
  }
}
resume();
</script>

<style scoped>
.filter-wrapper {
  display: flex;
  flex-direction: column;
}

.filter {
  display: flex;
  flex-direction: row;
  align-items: center;

  border-radius: var(--el-border-radius-base);
  color: var(--el-color-primary);
  border: 1px solid var(--el-border-color);
  padding: 1rem;
  margin: 0.75rem 0;
  width: 100%;
}

.filter-column {
  width: 18rem;
}

.filter-operator {
  margin: 0 1rem;
  width: 15rem;
}

.filter-input {
  margin-right: 1rem;
  width: 100%;
}
</style>
