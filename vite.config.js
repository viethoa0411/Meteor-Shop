import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Page-specific entries
                'resources/css/home.css',
                'resources/js/home.js',
                'resources/css/product-detail.css',
                'resources/js/product-detail.js',
                'resources/css/plp.css',
                'resources/js/plp.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
