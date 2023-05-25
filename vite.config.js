import { fileURLToPath } from 'node:url';
import { defineConfig, splitVendorChunkPlugin } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
import vue from '@vitejs/plugin-vue';
/* eslint-disable-next-line n/file-extension-in-import */
import ElementPlus from 'unplugin-element-plus/vite';
import basicSsl from '@vitejs/plugin-basic-ssl';

export default defineConfig({
  plugins: [
    basicSsl(), // https://vitejs.dev/config/server-options.html#server-https
    vue(),
    splitVendorChunkPlugin(), // https://vitejs.dev/guide/build.html#chunking-strategy
    /* eslint-disable-next-line new-cap */
    ElementPlus({
      useSource: true,
      defaultLocale: 'ru',
    }), // https://element-plus.org/en-US/guide/theming.html#by-scss-variables
    symfonyPlugin({
      viteDevServerHostname: 'localhost',
    }), // https://github.com/lhapaipai/vite-plugin-symfony & https://github.com/lhapaipai/vite-bundle
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
    reportCompressedSize: true,
    emptyOutDir: true,
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
  server: {
    https: true,
  },
});
