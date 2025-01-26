import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve:{
        alias:{
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
    base: '/build/',
    build: {
        manifest: true,
        outDir: 'build',
    },
    server: {
      host: '127.0.0.1', // Bind to localhost instead of ::1 (IPv6)
      port: 5173, // Vite default port
      strictPort: true,
      hmr: {
        host: 'localhost' // Ensure hot module reloading binds to localhost
      }
    }
});
