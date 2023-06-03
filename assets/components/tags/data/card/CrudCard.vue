<template>
  <base-card :loaded="!disabled">
    <template #header-skeleton>
      <div class="header">
        <div class="tags">
          <el-tag type="info">ID: {{ id }}</el-tag>
        </div>
        <div class="buttons">
          <el-button link type="warning" loading>Изменить</el-button>
          <el-button link type="danger" loading>Удалить</el-button>
        </div>
      </div>
    </template>

    <template #header>
      <div class="header">
        <div class="tags">
          <el-tag type="info">ID: {{ id }}</el-tag>
          <el-tag type="danger" v-if="isDeleted"> Удалено </el-tag>
        </div>
        <div class="buttons">
          <el-button link type="warning" @click="$emit('edit')"> Изменить </el-button>
          <template v-if="isDeleted">
            <el-button link type="success" @click="$emit('delete')"> Восстановить </el-button>
          </template>
          <template v-else>
            <el-popconfirm title="Вы уверены, что хотите удалить это?" width="275" @confirm="$emit('delete')">
              <template #reference>
                <el-button link type="danger">Удалить</el-button>
              </template>
            </el-popconfirm>
          </template>
        </div>
      </div>
    </template>

    <template #skeleton>
      <slot name="skeleton"></slot>
    </template>

    <slot></slot>
  </base-card>
</template>

<script setup lang="ts">
import { ElButton, ElTag, ElPopconfirm } from 'element-plus';
import { BaseCard } from '@/components/tags/base';

export interface Props {
  isDeleted: boolean;
  id: number;
  disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
  disabled: false,
});

defineEmits(['edit', 'delete']);
</script>

<style scoped>
.header {
  display: flex;
  flex-direction: row;
  width: 100%;
}

.tags {
  display: flex;
  flex-direction: column;
}

.tags .el-tag:nth-child(1) {
  margin-bottom: 0.5rem;
}

.buttons {
  margin-left: auto;
  margin-right: 0;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.buttons .el-button:nth-child(1) {
  margin-bottom: 1rem;
}
</style>
