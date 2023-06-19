<template>
  <div>
    <el-popover :width="350" title="Профиль" trigger="click">
      <template #reference>
        <base-avatar :src="user !== undefined && user.image ? user.image : ''" style="cursor: pointer" :size="size" />
      </template>

      <div class="popover">
        <div v-if="user !== undefined" class="container">
          <base-avatar :size="60" :src="user.image ? user.image : ''" class="avatar" />
          <div>
            <p class="text-main">{{ user.fullName }}</p>
            <p class="text">
              <i>{{ user.email }}</i>
            </p>
            <p class="text">
              <el-tag v-for="role in user.roles" :key="role" style="margin-right: 0.4rem">
                @{{ role.toLocaleLowerCase().replace('role_', '') }}
              </el-tag>
            </p>
          </div>
        </div>
      </div>
    </el-popover>
  </div>
</template>

<script setup lang="ts">
import { BaseAvatar } from '@/components/tags/base';
import type { UserProfile } from '@/composables';
import { ElPopover, ElTag } from 'element-plus';

export interface Props {
  user: UserProfile | undefined;
  size?: number;
}

withDefaults(defineProps<Props>(), {
  size: 24,
});
</script>

<style scoped>
.popover {
  display: flex;
  gap: 1rem;
  flex-direction: column;
}

.popover .container {
  display: flex;
  flex-direction: row;
}

.popover .avatar {
  margin-right: 1rem;
  margin-bottom: 0.5rem;
  flex-shrink: 0;
}

.popover .text-main {
  margin-top: 0;
  font-weight: 600;
  margin-bottom: 1rem;
}

.popover .text {
  font-size: 0.8rem;
  color: var(--el-color-info);
  align-self: flex-start;
  word-break: keep-all;
  text-align: left;
}
</style>
