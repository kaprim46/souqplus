import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';

export default defineConfig({
  plugins: [
    vue(),
    i18n(),
    laravel({
      input: ['resources/scss/app.scss', 'resources/js/app.ts'],
      refresh: true,
    }),
  ],
  optimizeDeps: {
    include: ['@fawmi/vue-google-maps', 'fast-deep-equal'],
  },
  server: {
    cors: {
      origin: ['http://soqplus.test', 'http://127.0.0.1:8000'],
    },
  },
});
