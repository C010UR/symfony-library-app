/* eslint-disable @typescript-eslint/no-unsafe-call */
import vue from '@vitejs/plugin-vue';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony'; // https://github.com/lhapaipai/vite-plugin-symfony & https://github.com/lhapaipai/vite-bundle

import elementPlus from 'unplugin-element-plus/vite';
import components from 'unplugin-vue-components/vite';

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
      defaultLocale: 'en',
    }),
    // basicSsl(),
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
          'element-plus-icons': ['@element-plus/icons-vue'],
          'element-plus': ['element-plus'],
          modules: [
            'lodash',
            'path',
            'qs',
            'validator',
            'vue-draggable-plus',
            'axios',
            'axios-cache-interceptor',
            'vue-router',
            'pinia',
            '@vueuse/core',
          ],
        },
      },
    },
  },
});
