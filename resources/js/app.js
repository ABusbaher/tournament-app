import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue/dist/vue.esm-bundler';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import AddTournamentForm from "@/Pages/Tournaments/Partials/AddTournamentForm.vue";
import AllTournaments from "@/Pages/Tournaments/AllTournaments.vue";
import EditTournamentForm from "@/Pages/Tournaments/Partials/EditTournamentForm.vue";
import BasePagination from "@/Components/BasePagination.vue";
import DeleteTournamentForm from "@/Pages/Tournaments/Partials/DeleteTournamentForm.vue";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

const app = createApp({
    components: {
        AddTournamentForm,
        AllTournaments,
        BasePagination,
        EditTournamentForm,
        DeleteTournamentForm,
    }
});
app.mount('#app');

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});


