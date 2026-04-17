import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/pages/sanctum-auth.ts',
                'resources/js/pages/owner-overview.ts',
                'resources/js/pages/owner-profile.ts',
                'resources/js/pages/owner-pets.ts',
                'resources/js/pages/owner-pet-edit.ts',
                'resources/js/pages/owner-pet-health-records.ts',
                'resources/js/pages/owner-appointments.ts',
                'resources/js/pages/receptionist-dashboard.ts',
                'resources/js/pages/receptionist-appointments.ts',
                'resources/js/pages/receptionist-appointment-detail.ts',
                'resources/js/pages/receptionist-billing.ts',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
