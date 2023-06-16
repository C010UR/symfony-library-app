<template>
  <el-dialog
    :model-value="modelValue"
    @update:model-value="emit => $emit('update:modelValue', emit)"
    :before-close="handleBeforeClose"
    destroy-on-close
    :fullscreen="isFullscreen"
  >
    <template #header>
      <template v-if="type === 'create'">
        <h4 class="title">Создание</h4>
      </template>
      <template v-else>
        <h4 class="title">
          Изменение <el-tag>ID: {{ id ? id : -1 }}</el-tag>
        </h4>
      </template>
    </template>
    <slot></slot>
    <div class="buttons">
      <el-button @click="clickCancel()" type="danger" plain :disabled="isLoading"> Отмена </el-button>
      <template v-if="type === 'create'">
        <el-button type="success" plain @click="$emit('submit')" :loading="isLoading"> Создать </el-button>
      </template>
      <template v-else>
        <el-button type="warning" plain @click="$emit('submit')" :loading="isLoading"> Изменить </el-button>
      </template>
    </div>
  </el-dialog>
</template>

<script setup lang="ts">
import { ElDialog, ElButton, ElTag } from 'element-plus';
import { onMounted, onUnmounted, ref } from 'vue';

export interface CreateFormProps {
  modelValue: boolean;
  type: 'create';
  isLoading?: boolean;
}

export interface UpdateFormProps {
  modelValue: boolean;
  type: 'update';
  id?: number;
  isLoading?: boolean;
}

export type Props = CreateFormProps | UpdateFormProps;

const props = withDefaults(defineProps<Props>(), {
  isLoading: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', isOpen: boolean): void;
  (e: 'submit', isSubmitted: true): void;
}>();

function handleBeforeClose(done: () => void) {
  if (!props.isLoading) {
    done();
  }

  return null;
}

function clickCancel() {
  emit('update:modelValue', false);
}

const isFullscreen = ref(false);

function setIsFullscreen() {
  isFullscreen.value = window.innerWidth <= 992;
}

onMounted(() => {
  window.addEventListener('resize', setIsFullscreen);
});

onUnmounted(() => {
  window.removeEventListener('resize', setIsFullscreen);
});
</script>

<style scoped>
.buttons {
  display: flex;
  flex-direction: row;
  justify-content: end;
  margin-top: 1rem;
}

.title {
  margin: 0;
}
</style>
