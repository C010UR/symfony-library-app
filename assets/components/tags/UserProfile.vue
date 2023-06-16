<template>
  <el-popover :width="350" title="Профиль" trigger="click">
    <template #reference>
      <base-avatar :src="profile !== undefined && profile.image ? profile.image : ''" style="cursor: pointer" />
    </template>

    <div class="popover">
      <div v-if="profile !== undefined" class="container">
        <base-avatar :size="60" :src="profile.image ? profile.image : ''" class="avatar" />
        <div>
          <p class="text-main">{{ profile.fullName }}</p>
          <p class="text">
            <i>{{ profile.email }}</i>
          </p>
          <p class="text">
            <el-tag v-for="role in profile.roles" :key="role" style="margin-right: 0.4rem">
              @{{ role.toLocaleLowerCase().replace('role_', '') }}
            </el-tag>
          </p>
        </div>
      </div>
      <div v-else class="container">
        <base-avatar :size="60" class="avatar" />
        <div>
          <p class="text-main">Авторизоваться</p>
          <p class="text">
            <i>Для получения доступа к большему количеству возможностей, авторизуйтесь.</i>
          </p>
        </div>
      </div>

      <div class="buttons">
        <el-button v-if="profile !== undefined" @click="btnLogout">Выйти</el-button>
        <el-button v-else @click="btnLogin">Войти</el-button>
      </div>
    </div>
  </el-popover>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { ElPopover, ElTag, ElButton } from 'element-plus';
import { BaseAvatar } from '@/components/tags/base';
import { useGetProfile, useLogout } from '@/composables';
import type { UserProfile } from '@/composables';

const router = useRouter();

const profile = ref<UserProfile | undefined>();

const emits = defineEmits<{
  (e: 'load-profile', profile: UserProfile | undefined): void;
}>();

async function btnLogout() {
  await useLogout();
  router.push({ name: 'Main' });
}

function btnLogin() {
  router.push({ name: 'Login' });
}

onMounted(async () => {
  profile.value = await useGetProfile();
  emits('load-profile', profile.value);
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

.popover .buttons {
  align-items: flex-end;
  display: flex;
  flex-direction: column;
  width: 100%;
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
