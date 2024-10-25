import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue/dist/vue.esm-bundler';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
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
import AllByFixtures from "@/Pages/Games/AllByFixtures.vue";
import EditGameScore from "@/Pages/Games/Partials/EditFixtureGameScore.vue";
import EliminationGames from "@/Pages/Games/EliminationGames.vue";
import AdminAllByEliminationCup from "@/Pages/Games/AdminAllByEliminationCup.vue";
import Navigation from "@/Layouts/Navigation.vue";
import AdminNavigation from "@/Layouts/AdminNavigation.vue";
import SetFixturePassword from "@/Pages/Games/Partials/SetFixturePassword.vue";
import FixtureLogin from "@/Pages/Games/Partials/FixtureLogin.vue";
import Spinner from "@/Components/Spinner.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import { Ziggy } from './ziggy.js';
import '@vuepic/vue-datepicker/dist/main.css'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
library.add(fas);

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
        AllByFixtures,
        EditGameScore,
        EliminationGames,
        AdminAllByEliminationCup,
        Navigation,
        AdminNavigation,
        SetFixturePassword,
        FixtureLogin,
        Spinner,
        FontAwesomeIcon,
        VueDatePicker,
        ZiggyVue
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


