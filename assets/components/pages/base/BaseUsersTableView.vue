<template>
  <filter-drawer :columns="columns" v-model:orders="orders" v-model:filters="filters" :disabled="isNoData" />

  <create-user-form v-model="isCreateFormOpen" @submit="submitCreate" />
  <update-user-form v-model="isUpdateFormOpen" @submit="submitUpdate" :entity="updateEntity" />

  <table-list
    :can-create="canCreate"
    :can-update="canUpdate"
    :can-delete="canDelete"
    :data="data"
    withMeta
    @click:delete="handleDelete"
    @click:update="handleUpdate"
    @click:create="handleCreate"
  >
    <template #expanded> </template>
    <el-table-column label="Profile Image">
      <template #default="prop">
        <base-avatar :size="32" :src="prop.row.image" class="avatar" />
      </template>
    </el-table-column>
    <el-table-column label="Full Name">
      <template #default="prop">
        <span class="row-text">
          {{ prop.row.fullName }}
        </span>
      </template>
    </el-table-column>
    <el-table-column label="Email">
      <template #default="prop">
        <span class="row-text">
          {{ prop.row.email }}
        </span>
      </template>
    </el-table-column>
    <el-table-column label="Roles">
      <template #default="prop">
        <el-tag v-for="role in prop.row.roles" :key="role" class="tag">
          @{{ role.toLocaleLowerCase().replace('role_', '') }}
        </el-tag>
        <el-tag v-if="prop.row.isActive" type="success" class="tag">Active</el-tag>
        <el-tag v-else type="danger" class="tag">Not Active</el-tag>
      </template>
    </el-table-column>
  </table-list>

  <data-pagination v-model="pagination" :disabled="isNoData" />
</template>

<script setup lang="ts">
import { popup } from '@/components/tags';
import { BaseAvatar } from '@/components/tags/base';
import { DataPagination, FilterDrawer, TableList } from '@/components/tags/data';
import { CreateUserForm, UpdateUserForm } from '@/components/tags/form';
import type { ApiMeta, ApiParams, ApiUrl, Filter, FilterOption, Order, RouteParams, UserProfile } from '@/composables';
import {
  ApiUrls,
  useChangeQuery,
  useDelete,
  useGetAll,
  useGetMeta,
  useParseApiParams,
  useParseParams,
} from '@/composables';
import { ElMessageBox, ElTableColumn, ElTag } from 'element-plus';
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

export interface Props {
  canCreate?: boolean;
  canUpdate?: boolean;
  canDelete?: boolean;
  url?: ApiUrl | string;
  metaUrl?: ApiUrl | string;
}

const props = withDefaults(defineProps<Props>(), {
  canCreate: false,
  canUpdate: false,
  canDelete: false,
  url: ApiUrls.users,
  metaUrl: ApiUrls.users,
});

const emit = defineEmits<{
  (e: 'update', isUpdated: true): void;
}>();

const route = useRoute();

const params = useParseApiParams(route.query as ApiParams);

const filters = ref<Filter[] | undefined>(params?.filters);
const orders = ref<Order[] | undefined>(params?.orders);
const pagination = ref<ApiMeta>({
  offset: 0,
  pageSize: 20,
  paginated: true,
  totalCount: 0,
  ...params?.pagination,
});
const columns = ref<FilterOption[]>([]);
const data = ref<UserProfile[] | undefined>();
const watchDisabled = ref<boolean>(false);

const isNoData = computed(() => {
  return data.value === undefined;
});

async function getData() {
  const parsedParams = useParseParams(filters.value, orders.value, {
    offset: pagination.value.offset,
    pageSize: pagination.value.pageSize,
  });

  useChangeQuery(route.path, parsedParams as object);

  data.value = undefined;
  const _data = await useGetAll<UserProfile>(props.url, parsedParams as RouteParams);

  emit('update', true);

  if (!_data) {
    data.value = [];
    return;
  }

  data.value = _data.data;
  pagination.value = _data.meta;
}

onMounted(async () => {
  columns.value = (await useGetMeta(props.metaUrl)) ?? [];

  watchDisabled.value = true;
  await getData();
  watchDisabled.value = false;
});

watch([filters, orders, pagination], async () => {
  if (!watchDisabled.value) {
    watchDisabled.value = true;
    await getData();
    watchDisabled.value = false;
  }
});

const isCreateFormOpen = ref<boolean>(false);
const isUpdateFormOpen = ref<boolean>(false);
const updateEntity = ref<UserProfile | undefined>();

async function handleDelete(user: UserProfile) {
  if (user.isDeleted) {
    await useDelete(props.url, user);
    await getData();
    return;
  }

  ElMessageBox.alert(`Are you sure you want to delete '${user.fullName}'?`, `Delete User`, {
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    type: 'warning',
    async callback(action: string) {
      if (action === 'confirm') {
        if (await useDelete(props.url, user)) {
          popup('success', 'User deleted successfully!');
        } else {
          popup('error', 'An error occurred during the User deletion');
        }

        await getData();
      }
    },
  });
}

function handleUpdate(user: UserProfile) {
  updateEntity.value = { ...user };
  isUpdateFormOpen.value = true;
}

async function submitUpdate() {
  isUpdateFormOpen.value = false;
  await getData();
}

function handleCreate() {
  isCreateFormOpen.value = true;
}

async function submitCreate() {
  isCreateFormOpen.value = false;
  await getData();
}
</script>

<style scoped>
.expanded-container {
  margin: 1rem;
  display: flex;
  flex-direction: column;
}

.row-text {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  text-overflow: ellipsis;
}

.avatar {
  flex-shrink: 0;
}

.text {
  font-size: 1.125rem;
  display: flex;
  flex-direction: row;
  margin: 0.5rem 0;
  align-items: center;
}

.text > * {
  margin: 0;
  margin-right: 0.3rem;
}

.tag {
  margin: 0.2rem;
}
</style>
