<script setup>
import {onMounted, reactive, ref, toRefs} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import BasePagination from "@/Components/BasePagination.vue";
import EditGameScore from "@/Pages/Games/Partials/EditFixtureGameScore.vue";
import StatusMessage from "@/Components/StatusMessage.vue";
import AppTabs from "@/Components/AppTabs.vue";
import LeagueTable from "@/Components/LeagueTable.vue";
import SetFixturePassword from "@/Pages/Games/Partials/SetFixturePassword.vue";
import FixtureLogin from "@/Pages/Games/Partials/FixtureLogin.vue";
import Spinner from "@/Components/Spinner.vue";
import {useDateTimeFormatter} from "@/composables/useDateTimeFormatter.js";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    fixtureId: {
        type: String,
        required: true,
    },
});
const games = ref([]);
const user = window.Laravel?.user;
const tournamentStore = useTournamentStore();
tournamentStore.setIdFromUrl();
const tournamentId = tournamentStore.getId;
const tournamentName = ref(tournamentStore.getName);
const currentPage = ref(1);
const nextPageLink = ref('');
const previousPageLink = ref('');
const total = ref(1);
const isPasswordProtected = ref(true);
const loading = ref(true);
const { formatDate } = useDateTimeFormatter();

const messages = reactive({
    updateGameScore: false,
    updateFixturePassword: false,
});

const showMessage = (type) => {
    messages[type] = true;
    setTimeout(() => {
        messages[type] = false;
    }, 5000);
};

const fetchGames = (url) => {
    if (url !== null) {
        axios.get(url)
            .then(response => {
                games.value = response.data.games;
                isPasswordProtected.value = response.data.isPasswordProtected;
                currentPage.value = response.data.fixtures.fixture;
                nextPageLink.value = response.data.fixtures.next_fixture ?
                    `/api/tournaments/${tournamentId}/fixtures/${response.data.fixtures.next_fixture}` : null;
                previousPageLink.value = response.data.fixtures.prev_fixture ?
                    `/api/tournaments/${tournamentId}/fixtures/${response.data.fixtures.prev_fixture}` : null;
                total.value = response.data.fixtures.max_fixture;
            })
            .catch(error => {
                console.log(error);
            });
    }
};
const handleScoreUpdate = () => {
    fetchGames(`/api/tournaments/${tournamentId}/fixtures/${currentPage.value}`);
    showMessage("updateGameScore");
}
const { fixtureId } = toRefs(props);

onMounted(async() => {
    await tournamentStore.getByTournamentById(tournamentId);
    tournamentName.value = tournamentStore.getName;
    await fetchGames(`/api/tournaments/${tournamentId}/fixtures/${fixtureId.value}`);
    loading.value = false;
});

const handlePasswordUpdated = () => {
    fetchGames(`/api/tournaments/${tournamentId}/fixtures/${fixtureId.value}`);
    showMessage("updateFixturePassword");
};

const loginSuccessfully = () => {
    isPasswordProtected.value = false;
};


const tabList = ["Utakmice", "Tabela"];

const teamsRanking = reactive([]);
const fetchTable = () => {
    axios.get(`/api/tournaments/${tournamentId}/table`)
        .then(response => {
            teamsRanking.value = response.data;
        })
        .catch(error => {
            console.log(error);
        });
};

</script>
<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <Spinner v-if="loading" />
        <div v-else-if="(!user ||(user && user.role !== 'admin')) && isPasswordProtected">
            <fixture-login :fixture-id="fixtureId" :tournament-id="tournamentId" @password-submitted="loginSuccessfully" />
        </div>
        <app-tabs v-else class="w-11/12 lg:w-10/12 mx-auto mb-16" :tabList="tabList" @handle-click-second-tab="fetchTable">
            <template v-slot:tabPanel-1>
                <!-- Modern Header Section -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-6">
                        <span class="text-white font-semibold text-lg uppercase tracking-wider">Kolo {{ currentPage }}</span>
                    </div>
                    <div class="relative">
                        <!-- Darker background for better contrast -->
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                        <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                            <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                                Utakmice u ligi {{ tournamentName }}
                            </h1>
                            <p class="text-gray-300 mt-4 text-lg font-medium">Pratite sve rezultate i događaje</p>
                        </div>
                    </div>
                </div>

                <!-- Status Messages -->
                <StatusMessage message="Lozinka za kolo uspešno izmenjena" color="green" :show="messages.updateFixturePassword"
                               @close="messages.updateFixturePassword = false"/>
                <StatusMessage message="Rezultat utakmice uspešno izmenjen" color="green" :show="messages.updateGameScore"
                               @close="messages.updateGameScore = false"/>

                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center space-x-4">
                        <!-- Previous Fixture Button -->
                        <Link v-if="previousPageLink"
                              :href="`/tournaments/${tournamentId}/fixtures/${currentPage - 1}`"
                              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Prethodno kolo
                        </Link>

                        <!-- Next Fixture Button -->
                        <Link v-if="nextPageLink"
                              :href="`/tournaments/${tournamentId}/fixtures/${currentPage + 1}`"
                              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-green-500/30 hover:border-green-400/50">
                            Sledeće kolo
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Set Fixture Password -->
                    <set-fixture-password v-if="user && user.role === 'admin'" @passwordUpdated="handlePasswordUpdated" :fixture-id="fixtureId" :tournament-id="tournamentId"/>
                </div>

                <!-- Games Grid -->
                <div v-if="games.length" class="space-y-6">
                    <div v-for="(game, index) in games" :key="game.id"
                         class="transform transition-all duration-500 hover:scale-105"
                         :style="{ animationDelay: `${index * 100}ms` }">

                        <!-- Match Card -->
                        <div class="relative overflow-hidden bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                            <!-- Animated Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 animate-pulse"></div>

                            <div class="relative p-8">
                                <div class="flex flex-col lg:flex-row items-center justify-between">

                                    <!-- Host Team -->
                                    <div class="flex-1 flex flex-col items-center mb-6 lg:mb-0" :title="game.host_team_name">
                                        <div class="relative group">
                                            <div class="w-32 h-32 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center border-4 border-gray-600/50 group-hover:border-blue-500/50 transition-all duration-300 shadow-lg">
                                                <img v-if="game.host_team_image"
                                                     class="object-cover w-28 h-28 rounded-full"
                                                     :src="game.host_team_image"
                                                     alt="Team Image" />
                                                <div v-else class="text-center p-4">
                                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <p class="text-gray-400 text-xs">Nema logo</p>
                                                </div>
                                            </div>
                                            <!-- Glow effect on hover -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        </div>
                                        <h2 class="team-name mt-6 text-xl font-bold text-white text-center max-w-48 truncate">{{ game.host_team_name }}</h2>
                                    </div>

                                    <!-- Match Details -->
                                    <div v-if="game.guest_team_name" class="flex-1 flex flex-col items-center px-8">
                                        <div class="text-center">
                                            <!-- Game Number -->
                                            <div class="mb-4">
                                                <p class="date-caption text-gray-300 font-medium">
                                                    Utakmica {{ index + 1 }}
                                                </p>
                                            </div>

                                            <!-- Score Display -->
                                            <div class="relative">
                                                <div class="flex items-center justify-center space-x-4">
                                                    <div class="score-box">
                                                        <span class="match-score-number">
                                                            {{ game.host_goals !== null ? game.host_goals : '-' }}
                                                        </span>
                                                    </div>

                                                    <div class="flex flex-col items-center">
                                                        <span class="text-gray-400 text-sm font-medium mb-1">VS</span>
                                                        <div class="w-12 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></div>
                                                    </div>

                                                    <div class="score-box">
                                                        <span class="match-score-number">
                                                            {{ game.guest_goals !== null ? game.guest_goals : '-' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Status Badge -->
                                                <div class="mt-4">
                                                    <span v-if="game.host_goals !== null"
                                                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                                        Završeno
                                                    </span>
                                                    <span v-else
                                                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                                                        Na čekanju
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Admin Edit Button -->
                                            <div class="mt-4">
                                                <edit-game-score v-if="user && user.role === 'admin'"
                                                                @score-updated="handleScoreUpdate"
                                                                :game-id="game.id"
                                                                :edited-score="game.host_goals !== null" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Free Team Placeholder -->
                                    <div v-else class="flex-1 flex flex-col items-center px-8">
                                        <div class="text-center">
                                            <div class="relative group">
                                                <div class="w-32 h-32 bg-gradient-to-br from-gray-700/50 to-gray-800/50 rounded-full flex items-center justify-center border-2 border-dashed border-gray-600/50 group-hover:border-gray-500/50 transition-all duration-300 shadow-lg">
                                                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                                <!-- Subtle glow effect on hover -->
                                                <div class="absolute inset-0 bg-gradient-to-r from-gray-500/10 to-gray-600/10 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            </div>
                                            <h2 class="team-name mt-6 text-xl font-bold text-gray-400 text-center max-w-48 truncate">
                                                Slobodan tim
                                            </h2>
                                        </div>
                                    </div>

                                    <!-- Guest Team -->
                                    <div v-if="game.guest_team_name" class="flex-1 flex flex-col items-center" :title="game.guest_team_name">
                                        <div class="relative group">
                                            <div class="w-32 h-32 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center border-4 border-gray-600/50 group-hover:border-purple-500/50 transition-all duration-300 shadow-lg">
                                                <img v-if="game.guest_team_image"
                                                     class="object-cover w-28 h-28 rounded-full"
                                                     :src="game.guest_team_image"
                                                     alt="Team Image" />
                                                <div v-else class="text-center p-4">
                                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <p class="text-gray-400 text-xs">Nema logo</p>
                                                </div>
                                            </div>
                                            <!-- Glow effect on hover -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        </div>
                                        <h2 class="team-name mt-6 text-xl font-bold text-white text-center max-w-48 truncate">{{ game.guest_team_name }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-300 mb-2">Nema utakmica</h3>
                    <p class="text-gray-500">Trenutno nema zakazanih utakmica za ovo kolo.</p>
                </div>
            </template>
            <template v-slot:tabPanel-2>
                <league-table :teams="teamsRanking.value"></league-table>
            </template>
        </app-tabs>
    </div>
</template>

<style scoped>
.date-caption {
    font-size: 1.1rem;
    color: #d1d5db;
    font-weight: 600;
}

.team-name {
    transition: all 0.3s ease;
}

.team-name:hover {
    color: #60a5fa;
    transform: translateY(-2px);
}

.match-score-number {
    font-size: 3rem;
    font-weight: 900;
    color: #ffffff;
    text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
    transition: all 0.3s ease;
}

.score-box {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
    border: 2px solid rgba(59, 130, 246, 0.3);
    border-radius: 16px;
    padding: 1rem 2rem;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.score-box:hover {
    border-color: rgba(59, 130, 246, 0.6);
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
    transform: translateY(-2px);
}

/* Animation for cards appearing */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.transform {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .match-score-number {
        font-size: 2rem;
    }

    .score-box {
        padding: 0.75rem 1.5rem;
    }
}

@media (max-width: 40em) {
    h2 {
        margin-bottom: 0;
    }
}

/* Custom scrollbar for dark theme */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
}

::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}
</style>
