import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue/dist/vue.esm-bundler';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { createPinia } from 'pinia'
import AddTournamentForm from "@/Pages/Tournaments/Partials/AddTournamentForm.vue";
import AllTournaments from "@/Pages/Tournaments/AllTournaments.vue";
import EditTournamentNameForm from "@/Pages/Tournaments/Partials/EditTournamentNameForm.vue";
import BasePagination from "@/Components/BasePagination.vue";
import DeleteTournamentForm from "@/Pages/Tournaments/Partials/DeleteTournamentForm.vue";
import AllTeams from "@/Pages/Teams/AllTeams.vue";
import AddTeamForm from "@/Pages/Teams/Partials/AddTeamForm.vue";
import FileInput from "@/Components/FileInput.vue";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm.vue";
import EditTeamForm from "@/Pages/Teams/Partials/EditTeamForm.vue";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const pinia = createPinia()
const app = createApp({
    components: {
        AddTournamentForm,
        AllTournaments,
        BasePagination,
        EditTournamentNameForm,
        DeleteTournamentForm,
        AllTeams,
        AddTeamForm,
        FileInput,
        DeleteTeamForm,
        EditTeamForm,
    }
});
app.use(pinia)
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


