import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // @tailwindcss/vite (v4) O'CHIRILDI
        // Tailwind v3 postcss.config.js orqali ishlaydi
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
