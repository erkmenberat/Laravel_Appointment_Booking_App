import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Vite-Konfiguration für Laravel + Tailwind.
export default defineConfig({
    plugins: [
        // Definiert die Frontend-Einstiegspunkte und Auto-Refresh.
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Bindet Tailwind direkt in den Vite-Build ein.
        tailwindcss(),
    ],
    server: {
        watch: {
            // Ignoriert kompilierte Blade-Caches, um unnötige Rebuilds zu vermeiden.
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
