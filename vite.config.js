import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [vue()],
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: 'src/main.js',
            output: {
                format: 'iife', // Output as an Immediately Invoked Function Expression for browser compatibility
                entryFileNames: 'main.js',
                assetFileNames: 'style.css',
                globals: {
                    vue: 'Vue'
                }
            }
        },
        target: 'es2015' // Ensures compatibility with older browsers
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