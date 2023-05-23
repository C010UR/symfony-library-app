<template>
    <router-view v-slot="{ Component }">
        <transition :name="String($route.meta.transition) ?? ''">
            <component :is="Component"></component>
        </transition>
    </router-view>
</template>
