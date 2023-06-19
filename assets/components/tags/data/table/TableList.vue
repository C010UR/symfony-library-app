<template>
  <div class="wrapper">
    <el-table
      v-loading="data === undefined || data.length === 0"
      :data="data"
      stripe
      border
      style="width: 100%; padding: 0"
      table-layout="auto"
      row-key="id"
    >
      <slot name="expanded"></slot>
      <el-table-column label="№" v-if="withMeta">
        <template #default="props">
          <el-tag :type="props.row.isDeleted ? 'danger' : 'info'">{{ props.row.id ?? -1 }}</el-tag>
        </template>
      </el-table-column>
      <slot></slot>
      <el-table-column fixed="right" label="Действия" width="180" v-if="canCreate || canUpdate || canDelete">
        <template #header v-if="canCreate">
          <div style="display: flex; justify-content: center">
            <el-button type="primary" @click="handleAdd()"> Создать </el-button>
          </div>
        </template>
        <template #default="scope">
          <el-button link type="primary" size="small" @click="handleUpdate(scope.row)"> Изменить </el-button>
          <template v-if="canDelete">
            <el-popconfirm
              v-if="!scope.row.isDeleted"
              title="Вы уверены, что хотите удалить это?"
              @confirm="handleDelete(scope.row)"
            >
              <template #reference>
                <el-button link type="danger" size="small"> Удалить </el-button>
              </template>
            </el-popconfirm>
            <el-popconfirm v-else title="Вы уверены, что хотите восстановить это?" @confirm="handleDelete(scope.row)">
              <template #reference>
                <el-button link type="success" size="small"> Восстановить </el-button>
              </template>
            </el-popconfirm>
          </template>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script setup lang="ts" generic="T extends {id: number, isDeleted: boolean}">
import { ElTable, ElTableColumn, ElButton, ElPopconfirm, ElTag } from 'element-plus';

export interface Props {
  canCreate?: boolean;
  canUpdate?: boolean;
  canDelete?: boolean;
  withMeta?: boolean;
  data: T[] | undefined;
}

withDefaults(defineProps<Props>(), {
  canCreate: false,
  canUpdate: false,
  canDelete: false,
  withMeta: false,
});

const emit = defineEmits<{
  (e: 'click:create', isCreatePressed: true): void;
  (e: 'click:update', entity: T): void;
  (e: 'click:delete', entity: T): void;
}>();

const handleAdd = () => {
  emit('click:create', true);
};

const handleDelete = (entity: T) => {
  emit('click:delete', entity);
};

const handleUpdate = (entity: T) => {
  emit('click:update', entity);
};
</script>

<style scoped>
.wrapper {
  margin: 1rem;
}

.tag {
  margin: 0.2rem;
}
</style>
