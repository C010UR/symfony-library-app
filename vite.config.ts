/* eslint-disable @typescript-eslint/no-unsafe-call */

import { fileURLToPath } from 'node:url';
import { defineConfig, splitVendorChunkPlugin } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony'; // https://github.com/lhapaipai/vite-plugin-symfony & https://github.com/lhapaipai/vite-bundle
import vue from '@vitejs/plugin-vue';

import components from 'unplugin-vue-components/vite';
import elementPlus from 'unplugin-element-plus/vite';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue({
      template: {
        transformAssetUrls: {
          includeAbsolute: false,
        },
      },
    }),
    components({
      dts: './assets/components.d.ts',
    }),
    elementPlus({
      useSource: true,
      defaultLocale: 'ru',
    }),
    // basicSsl(),
    splitVendorChunkPlugin(),
    symfonyPlugin({
      viteDevServerHostname: 'localhost',
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('assets', import.meta.url)),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: '@use "@/styles/element/index.scss" as *;',
      },
    },
  },
  envDir: 'assets/env',
  build: {
    reportCompressedSize: true,
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: './assets/main.ts',
      },
      output: {
        manualChunks: {
          elementplus: ['element-plus', '@element-plus/icons-vue'],
          axios: ['axios', 'axios-cache-interceptor'],
          modules: ['lodash', 'path', 'qs', 'validator', 'vue-draggable-plus'],
          vue: ['vue-router', 'pinia', '@vueuse/core'],
          views: ['assets/views/index.ts'],
        },
      },
    },
  },
});
