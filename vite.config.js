import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
import vue from '@vitejs/plugin-vue';
/* eslint-disable-next-line n/file-extension-in-import */
import ElementPlus from 'unplugin-element-plus/vite';
import basicSsl from '@vitejs/plugin-basic-ssl';

export default defineConfig({
  plugins: [
    basicSsl(),
    vue(),
    /* eslint-disable-next-line new-cap */
    ElementPlus({
      useSource: true,
      defaultLocale: 'ru',
    }),
    symfonyPlugin({
      viteDevServerHostname: 'localhost',
    }),
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
  server: {
    host: '0.0.0.0',
    strictPort: true,
    port: 5173,
    https: true,
    watch: {
      usePolling: true,
    },
  },
});
