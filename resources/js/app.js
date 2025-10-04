import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { useAuthStore } from './stores/auth';
import { createPinia } from 'pinia';

const pinia = createPinia();

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Menggunakan Pinia sebagai plugin
        app.use(pinia);

        // Panggil metode initialize() dari authStore untuk memuat token
        // dari localStorage sebelum aplikasi dimuat.
        const authStore = useAuthStore();
        authStore.initialize();

        app.use(plugin)
           .mount(el);
    },
});
