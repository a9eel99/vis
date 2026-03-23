import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/dashboard.css',
                'resources/css/components.css',
                'resources/css/forms.css',
                'resources/css/tables.css',
                'resources/css/media.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/inspections.js',
                'resources/js/templates.js',
                'resources/js/media-upload.js',
            ],
            refresh: true,
        }),
    ],
});
