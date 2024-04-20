<template>
  <div class="wrapper">
    <el-table
      v-if="Array.isArray(data) && data.length > 0"
      v-loading="!data"
      :data="data"
      stripe
      border
      style="width: 100%; padding: 0"
      table-layout="auto"
      row-key="id"
    >
      <slot name="expanded"></slot>
      <el-table-column label="ID" v-if="withMeta">
        <template #default="props">
          <el-tag :type="props.row.isDeleted ? 'danger' : 'info'">{{ props.row.id ?? -1 }}</el-tag>
        </template>
      </el-table-column>
      <slot></slot>
      <el-table-column fixed="right" label="Действия" width="180" v-if="canCreate || canUpdate || canDelete">
        <template #header v-if="canCreate">
          <div style="display: flex; justify-content: center">
            <el-button type="primary" @click="handleAdd()"> Create </el-button>
          </div>
        </template>
        <template #default="scope">
          <el-button link type="primary" size="small" @click="handleUpdate(scope.row)"> Update </el-button>
          <template v-if="canDelete">
            <el-popconfirm
              v-if="!scope.row.isDeleted"
              title="Are you sure you want to delete this?"
              @confirm="handleDelete(scope.row)"
            >
              <template #reference>
                <el-button link type="danger" size="small"> Delete </el-button>
              </template>
            </el-popconfirm>
            <el-popconfirm v-else title="Are your sure you want to restore this?" @confirm="handleDelete(scope.row)">
              <template #reference>
                <el-button link type="success" size="small"> Restore </el-button>
              </template>
            </el-popconfirm>
          </template>
        </template>
      </el-table-column>
    </el-table>

    <el-empty v-else description="Empty" class="no-data" />
  </div>
</template>

<script setup lang="ts" generic="T extends {id: number, isDeleted: boolean}">
import { ElButton, ElPopconfirm, ElTable, ElTableColumn, ElTag, ElEmpty } from 'element-plus';

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
.no-data {
  margin: auto;
}

.wrapper {
  margin: 1rem;
}

.tag {
  margin: 0.2rem;
}
</style>
