import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
import vue from '@vitejs/plugin-vue';
import ElementPlus from 'unplugin-element-plus/vite.mjs';

export default defineConfig({
  plugins: [
    vue(),
    /* eslint-disable-next-line new-cap */
    ElementPlus({
      useSource: true,
      defaultLocale: 'ru',
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
  root: '.',
  base: '/build/',
  publicDir: false,
  build: {
    reportCompressedSize: true,
    manifest: true,
    emptyOutDir: true,
    assetsDir: '',
    outDir: './public/build',
    rollupOptions: {
      input: {
        app: './assets/app.js',
      },
      output: {
        manualChunks: {
          other: ['assets/api/index.js'],
          'views-auth': ['assets/views/Authentication/index.js'],
        },
      },
    },
  },
});
