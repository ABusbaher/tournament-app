<script setup>
import {onMounted, ref, computed} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage} from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const showingNavigationDropdown = ref(false);
const games = ref({});
const eliminationGames = ref({});
const tournaments = ref([]);
const user = window.Laravel?.user;
const fetchGames = () => {
    axios.get('/api/tournaments/getGames')
        .then(response => {
            games.value = response.data['fixtureGames'];
            eliminationGames.value = response.data['eliminationGames'];
        })
        .catch(error => {
            console.log(error);
        });
};

const inertiaUser = computed(() => {
    return usePage()?.props?.auth?.user
})

const fetchTournaments = () => {
    axios.get('/api/tournaments')
        .then(response => {
            tournaments.value = response.data.tournaments.data;
        })
        .catch(error => {
            console.log(error);
        });
};

const mobileLinkClasses = 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-blue-400 focus:outline-none focus:text-white focus:bg-gray-700 focus:border-blue-400 transition duration-150 ease-in-out';

onMounted(async() => {
    fetchGames();
    fetchTournaments();
});
</script>

<template>
    <div>
        <nav class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 border-b border-gray-700/50 shadow-lg">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('tournament.all')" class="group">
                                <div class="transform transition-all duration-300 group-hover:scale-105">
                                    <ApplicationLogo
                                        class="block h-14 w-auto"
                                    />
                                </div>
                            </Link>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                        <!-- League Games Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800/50 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-200"
                                        >
                                            <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'futbol']" />
                                            Utakmice u Ligi
                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4 text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="py-2">
                                        <a v-for="(value, key) in games" 
                                           :key="key"
                                           :href="route('fixture.games', {tournament: key, fixture: 1})" 
                                           class="flex items-center w-full px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50 transition-all duration-200 group">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3 group-hover:bg-blue-400 transition-all duration-200"></div>
                                            {{ value }}
                                        </a>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Cup Games Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800/50 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all duration-200"
                                        >
                                            <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'trophy']" />
                                            Kup Utakmice
                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4 text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="py-2">
                                        <a v-for="(value, key) in eliminationGames" 
                                           :key="key"
                                           :href="route('elimination.games', {tournament: key})" 
                                           class="flex items-center w-full px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50 transition-all duration-200 group">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3 group-hover:bg-purple-400 transition-all duration-200"></div>
                                            {{ value }}
                                        </a>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- User Dropdown -->
                        <div v-if="user || inertiaUser" class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800/50 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition-all duration-200"
                                        >
                                            <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'user']" />
                                            {{ user ? user.name : inertiaUser.name }}
                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4 text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="py-2">
                                        <DropdownLink :href="route('profile.edit')" class="text-gray-300 hover:text-white hover:bg-gray-700/50">
                                            <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'user']" />
                                            Profil
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-gray-300 hover:text-white hover:bg-gray-700/50">
                                            <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'arrow-right-from-bracket']" />
                                            Odjavi se
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Login Link -->
                        <div v-else class="flex items-center">
                            <Link :href="route('login')" 
                                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800/50 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition-all duration-200">
                                <font-awesome-icon class="w-4 h-4 mr-2" :icon="['fas', 'arrow-right-to-bracket']" />
                                Prijava
                            </Link>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-200"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden bg-gray-800/95 backdrop-blur-sm border-t border-gray-700/50"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <Link :href="route('tournament.all')" 
                          class="flex items-center px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 transition-all duration-200">
                        <font-awesome-icon class="w-5 h-5 mr-3" :icon="['fas', 'house']" />
                        Turniri
                    </Link>
                </div>

                <!-- Mobile Dropdowns -->
                <div class="pt-4 pb-1 border-t border-gray-700/50">
                    <div class="mt-3 space-y-1">
                        <!-- League Games Mobile -->
                        <div class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-400 mb-2">Utakmice u Ligi</div>
                            <div class="space-y-1">
                                <a v-for="(value, key) in games" 
                                   :key="key"
                                   :href="route('fixture.games', {tournament: key, fixture: 1})" 
                                   class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                                    {{ value }}
                                </a>
                            </div>
                        </div>

                        <!-- Cup Games Mobile -->
                        <div class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-400 mb-2">Kup Utakmice</div>
                            <div class="space-y-1">
                                <a v-for="(value, key) in eliminationGames" 
                                   :key="key"
                                   :href="route('elimination.games', {tournament: key})" 
                                   class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                                    {{ value }}
                                </a>
                            </div>
                        </div>

                        <!-- User Menu Mobile -->
                        <div v-if="user || inertiaUser" class="px-4 py-2 border-t border-gray-700/50">
                            <Link :href="route('profile.edit')" 
                                  class="flex items-center px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                                <font-awesome-icon class="w-5 h-5 mr-3" :icon="['fas', 'user']" />
                                Profil
                            </Link>
                            <Link :href="route('logout')" 
                                  method="post" 
                                  as="button"
                                  class="w-full flex items-center px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                                <font-awesome-icon class="w-5 h-5 mr-3" :icon="['fas', 'arrow-right-from-bracket']" />
                                Odjavi se
                            </Link>
                        </div>

                        <!-- Login Mobile -->
                        <div v-else class="px-4 py-2">
                            <Link :href="route('login')" 
                                  class="flex items-center px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                                <font-awesome-icon class="w-5 h-5 mr-3" :icon="['fas', 'arrow-right-to-bracket']" />
                                Prijava
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-gradient-to-r from-gray-800 to-gray-900 shadow-lg border-b border-gray-700/50" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main v-if="$slots.main">
            <slot name="main" />
        </main>
    </div>
</template>

<style scoped>
/* Custom scrollbar for mobile menu */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Hover effects */
button:hover, a:hover {
    transform: translateY(-1px);
}

/* Focus states */
button:focus, a:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
