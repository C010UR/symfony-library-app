<template>
    <div class="wrapper">
        <el-card class="container" shadow="hover">
            <div v-if="header" class="header">
                <h1 style="text">
                    <img :src="image" alt="Logo" class="logo" />
                    {{ header }}
                </h1>
                <dark-switch style="margin-right: 0; margin-left: auto" />
            </div>
            <el-divider></el-divider>
            <slot></slot>
        </el-card>
    </div>
</template>

<script setup>
import { ElDivider, ElCard } from 'element-plus';
import { DarkSwitch } from '~/components/tags/index.js';
import { getImageUrl } from '~/api/index.js';

const image = getImageUrl('/images/logo.png');

defineProps({
    header: {
        type: String,
        required: false,
        default: '',
    },
});
</script>

<style scoped>
.wrapper {
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.container {
    margin: 0;
    padding: 0rem 1rem;
    width: 40%;
    background-color: var(--el-bg-color);
    transition: var(--animation-duration);
}

.header {
    margin-top: 0.5rem;
    margin-bottom: 1rem;
    display: flex;
    flex-direction: row;
}
.header h1 {
    color: var(--el-text-color-primary);
    margin: 0;
    margin-bottom: 0.75rem;
}

.logo {
    height: 3rem;
    margin-right: 0.5rem;
    vertical-align: middle;
}

@media only screen and (max-width: 1200px) {
    .container {
        width: 60%;
    }
}

@media only screen and (max-width: 992px) {
    .container {
        width: 80%;
    }
}

@media only screen and (max-width: 768px) {
    .container {
        width: 100%;
    }
}

.dark .logo {
    filter: brightness(0) invert(1);
}
</style>