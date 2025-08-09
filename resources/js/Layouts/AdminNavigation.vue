<script setup>
import {onMounted, ref} from "vue";
import {route} from "ziggy-js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {Link} from "@inertiajs/vue3";

const games = ref({});
const eliminationGames = ref({});
const tournaments = ref([]);
const fixturesByTournament = ref({});
const user = window.Laravel.user;

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

const fetchFixtures = () => {
  axios.get('/api/tournaments/getFixtures')
      .then(response => {
        fixturesByTournament.value = response.data['fixturesByTournament'];
      })
      .catch(error => {
        console.log(error);
      });
};

const fetchTournaments = () => {
  axios.get('/api/tournaments')
      .then(response => {
        tournaments.value = response.data.tournaments.data;
      })
      .catch(error => {
        console.log(error);
      });
};

onMounted(async() => {
  fetchGames();
  fetchFixtures();
  fetchTournaments();
});
</script>

<template>
  <div class="absolute flex top-0 h-screen z-20">
    <button
        class="w-12 h-48 my-auto rounded-lg text-gray-300 bg-gradient-to-b from-gray-800/90 to-gray-900/90 hover:from-gray-700/90 hover:to-gray-800/90 focus:ring-4 focus:ring-gray-600/50 font-medium text-sm focus:outline-none transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-gray-700/50 backdrop-blur-sm"
        data-drawer-target="drawer-navigation" 
        data-drawer-show="drawer-navigation" 
        aria-controls="drawer-navigation"
    >
      <span class="block rotate-90 transform origin-center font-bold whitespace-nowrap flex justify-center items-center h-full w-full">
        Admin meni
      </span>
    </button>
  </div>

  <!-- Modern Drawer Component -->
  <div id="drawer-navigation" 
       class="fixed top-0 left-0 z-40 w-80 h-screen p-0 overflow-y-auto transition-transform -translate-x-full bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 border-r border-gray-700/50 shadow-2xl backdrop-blur-sm" 
       tabindex="-1" 
       aria-labelledby="drawer-navigation-label">
    
    <!-- Drawer Header -->
    <div class="p-6 border-b border-gray-700/50 bg-gray-800/50">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <div>
            <h5 id="drawer-navigation-label" class="text-lg font-bold text-white">Admin Panel</h5>
            <p class="text-sm text-gray-400">Upravljanje turnirima</p>
          </div>
        </div>
        <button type="button" 
                data-drawer-hide="drawer-navigation" 
                aria-controls="drawer-navigation" 
                class="text-gray-400 bg-transparent hover:bg-gray-700/50 hover:text-white rounded-lg text-sm p-2 transition-all duration-200">
          <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
          <span class="sr-only">Zatvori meni</span>
        </button>
      </div>
    </div>

    <!-- Drawer Content -->
    <div class="py-6 px-4 overflow-y-auto">
      <ul class="space-y-2 font-medium">
        <!-- Tournaments -->
        <li>
          <Link :href="route('tournament.all')" 
                class="flex items-center p-3 text-gray-300 rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200 group">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-blue-700 group-hover:to-blue-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
            </div>
            <span>Turniri</span>
          </Link>
        </li>

        <!-- Cup Games -->
        <li>
          <button type="button" 
                  class="flex items-center w-full p-3 text-gray-300 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group" 
                  aria-controls="elimination-games" 
                  data-collapse-toggle="elimination-games">
            <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-purple-700 group-hover:to-purple-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <span class="flex-1 text-left">Kup Utakmice</span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-all duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <ul id="elimination-games" class="hidden py-2 space-y-1 pl-11">
            <li v-for="(value, key) in eliminationGames" :key="key">
              <Link :href="route('elimination.games', {tournament: key})" 
                    class="flex items-center w-full p-2 text-gray-400 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group">
                <div class="w-2 h-2 bg-purple-500 rounded-full mr-3 group-hover:bg-purple-400 transition-all duration-200"></div>
                {{ value }}
              </Link>
            </li>
          </ul>
        </li>

        <!-- League Games -->
        <li>
          <button type="button" 
                  class="flex items-center w-full p-3 text-gray-300 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group" 
                  aria-controls="games" 
                  data-collapse-toggle="games">
            <div class="w-8 h-8 bg-gradient-to-r from-green-600 to-green-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-green-700 group-hover:to-green-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <span class="flex-1 text-left">Utakmice u Ligi</span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-all duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <ul id="games" class="hidden py-2 space-y-1 pl-11">
            <li v-for="(value, key) in games" :key="key">
              <div class="mb-2">
                <Link :href="route('fixture.games', {tournament: key, fixture: 1})" 
                      class="flex items-center w-full p-2 text-gray-400 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group">
                  <div class="w-2 h-2 bg-green-500 rounded-full mr-3 group-hover:bg-green-400 transition-all duration-200"></div>
                  {{ value }}
                </Link>
              </div>
              <!-- Fixture Links -->
              <div v-if="fixturesByTournament[key]" class="ml-4 space-y-1">
                <div v-for="fixture in fixturesByTournament[key].fixtures" :key="fixture" class="ml-4">
                  <Link :href="route('fixture.games', {tournament: key, fixture: fixture})" 
                        class="flex items-center w-full p-1 text-gray-500 transition-all duration-200 rounded hover:bg-gray-700/30 hover:text-gray-300 group text-xs">
                    <div class="w-1 h-1 bg-green-400 rounded-full mr-2 group-hover:bg-green-300 transition-all duration-200"></div>
                    Kolo {{ fixture }}
                  </Link>
                </div>
              </div>
            </li>
          </ul>
        </li>

        <!-- Edit Teams -->
        <li>
          <button type="button" 
                  class="flex items-center w-full p-3 text-gray-300 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group" 
                  aria-controls="edit-teams" 
                  data-collapse-toggle="edit-teams">
            <div class="w-8 h-8 bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-yellow-700 group-hover:to-yellow-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <span class="flex-1 text-left">Izmeni Timove</span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-all duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <ul id="edit-teams" class="hidden py-2 space-y-1 pl-11">
            <li v-for="tournament in tournaments" :key="tournament.id">
              <Link :href="route('team.all', {tournament: tournament.id})" 
                    class="flex items-center w-full p-2 text-gray-400 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3 group-hover:bg-yellow-400 transition-all duration-200"></div>
                {{ tournament.name }} timovi
              </Link>
            </li>
          </ul>
        </li>

        <!-- Edit Cup Games -->
        <li>
          <button type="button" 
                  class="flex items-center w-full p-3 text-gray-300 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group" 
                  aria-controls="edit-cup-games" 
                  data-collapse-toggle="edit-cup-games">
            <div class="w-8 h-8 bg-gradient-to-r from-red-600 to-red-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-red-700 group-hover:to-red-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <span class="flex-1 text-left">Izmeni Kup utakmice</span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-all duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <ul id="edit-cup-games" class="hidden py-2 space-y-1 pl-11">
            <li v-for="(value, key) in eliminationGames" :key="key">
              <Link :href="route('admin.elimination.games', {tournament: key})" 
                    class="flex items-center w-full p-2 text-gray-400 transition-all duration-200 rounded-lg hover:bg-gray-700/50 hover:text-white group">
                <div class="w-2 h-2 bg-red-500 rounded-full mr-3 group-hover:bg-red-400 transition-all duration-200"></div>
                Izmeni {{ value }}
              </Link>
            </li>
          </ul>
        </li>

        <!-- Profile -->
        <li>
          <Link :href="route('profile.edit')" 
                class="flex items-center p-3 text-gray-300 rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200 group">
            <div class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-indigo-700 group-hover:to-indigo-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <span>Profil</span>
          </Link>
        </li>

        <!-- Logout -->
        <li>
          <Link :href="route('logout')"
                method="post"
                as="button"
                class="w-full flex items-center p-3 text-gray-300 rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200 group">
            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-lg flex items-center justify-center mr-3 group-hover:from-gray-700 group-hover:to-gray-800 transition-all duration-200">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
            </div>
            <span>Odjavi se</span>
          </Link>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for drawer */
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
    transform: translateX(2px);
}

/* Focus states */
button:focus, a:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Drawer animation */
#drawer-navigation {
    transition: transform 0.3s ease-in-out;
}

/* Button glow effect */
button[data-drawer-target="drawer-navigation"]:hover {
    box-shadow: 0 0 20px rgba(75, 85, 99, 0.3);
}

/* Submenu animations */
ul[id] {
    transition: all 0.3s ease-in-out;
}

/* Icon container hover effects */
.group:hover .w-8.h-8 {
    transform: scale(1.1);
}
</style>
