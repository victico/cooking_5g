import { defineConfig } from 'vite';
import { fileURLToPath, URL } from "url";
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    // resolve: {
    //     alias: {
    //         "#": fileURLToPath(new URL("./resources/js/src", import.meta.url)),
    //     },
    // }
});
