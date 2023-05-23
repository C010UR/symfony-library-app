import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
import vue from '@vitejs/plugin-vue';
import ElementPlus from 'unplugin-element-plus/vite';

export default defineConfig({
  plugins: [
    vue(),
    /* eslint-disable-next-line new-cap */
    ElementPlus({
      useSource: true,
      defaultLocale: 'en-us',
    }),
    symfonyPlugin(),
  ],
  resolve: {
    alias: {
      '~/': fileURLToPath(new URL('assets', import.meta.url)) + '/',
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: '@use "~/styles/element/index.scss" as *;',
      },
    },
  },
  build: {
    rollupOptions: {
      input: {
        app: './assets/app.js',
      },
      output: {
        manualChunks: {
          other: [],
          'views-auth': ['./src/views/Authentication'],
        },
      },
    },
  },
});
