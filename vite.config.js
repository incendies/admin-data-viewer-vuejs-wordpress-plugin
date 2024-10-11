import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [vue()],
    build: {
      outDir: 'dist',
      rollupOptions: {
        input: 'src/main.js',
        output: {
          entryFileNames: 'main.js',
          assetFileNames: 'style.css'
        }
      }
    },
    server: {
      host: true,
      proxy: {
        '/api': {
          target: 'https://miusage.com/v1/challenge/2/static/',
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api/, ''),
        }
      }
    }
  });