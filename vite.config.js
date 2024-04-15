import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            buildDirectory: './bundle',
            input: [
                'resources/css/app.css',
                'resources/css/app.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true
        }
    },
});
