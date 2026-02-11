<script setup>
import {onMounted, reactive, ref} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import EditEliminationGameScore from "@/Pages/Games/Partials/EditEliminationGameScore.vue";
import StatusMessage from "@/Components/StatusMessage.vue";
import {useDateTimeFormatter} from "@/composables/useDateTimeFormatter.js";

const games = ref([]);
const tournamentStore = useTournamentStore();
tournamentStore.setIdFromUrl();
const tournamentId = tournamentStore.getId;
tournamentStore.getByTournamentById(tournamentId);
const tournamentName = ref(tournamentStore.getName);
const { formatDate } = useDateTimeFormatter();

const messages = reactive({
    updateGameScore: false,
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
            })
            .catch(error => {
                console.log(error);
            });
    }
};

const handleScoreUpdate = () => {
    fetchGames(`/api/tournaments/${tournamentId}/elimination-games`);
    showMessage("updateGameScore");
};

const getRoundName = (value) => {
    switch (value) {
        case 5:
            return '1/16 finala';
        case 4:
            return '1/8 finala';
        case 3:
            return 'Četvrtfinale';
        case 2:
            return 'Polufinale';
        case 1:
            return 'Finale';
        default:
            return 'Nepoznato kolo';
    }
};

onMounted(async() => {
    await tournamentStore.getByTournamentById(tournamentId);
    tournamentName.value = tournamentStore.getName;
    await fetchGames(`/api/tournaments/${tournamentId}/elimination`);
});

</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <!-- Modern Header -->
        <div class="text-center mb-12 pt-8">
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 rounded-full mb-6">
                <span class="text-white font-semibold text-sm uppercase tracking-wider">Admin Panel</span>
            </div>
            <div class="relative">
                <!-- Darker background for better contrast -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                        Ažuriraj utakmice turnira {{ tournamentName }}
                    </h1>
                    <p class="text-gray-300 mt-4 text-lg font-medium">Upravljajte rezultatima kup utakmica</p>
                    <StatusMessage message="Rezultat utakmice uspešno izmenjen" color="green" :show="messages.updateGameScore"
                                   @close="messages.updateGameScore = false"/>
                </div>
            </div>
        </div>

        <!-- Games Grid -->
        <div v-if="games.length" class="space-y-6">
            <div v-for="(game, index) in games" :key="game.id"
                 class="transform transition-all duration-500 hover:scale-105"
                 :style="{ animationDelay: `${index * 100}ms` }">

                <!-- Match Card -->
                <div class="relative overflow-hidden bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 via-orange-500/10 to-yellow-500/10 animate-pulse"></div>

                    <div class="relative p-8">
                        <div class="flex flex-col lg:flex-row items-center justify-between">

                            <!-- Team 1 -->
                            <div class="flex-1 flex flex-col items-center mb-6 lg:mb-0" :title="game.team1_name">
                                <div class="relative group">
                                    <div class="w-32 h-32 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center border-4 border-gray-600/50 group-hover:border-red-500/50 transition-all duration-300 shadow-lg">
                                        <img v-if="game.team1_image"
                                             class="object-cover w-28 h-28 rounded-full"
                                             :src="game.team1_image"
                                             alt="Team Image" />
                                        <div v-else class="text-center p-4">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <p class="text-gray-400 text-xs">Nema logo</p>
                                        </div>
                                    </div>
                                    <!-- Glow effect on hover -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                                <h2 class="team-name mt-6 text-xl font-bold text-white text-center max-w-48 truncate">{{ game.team1_name }}</h2>
                            </div>

                            <!-- Match Details -->
                            <div class="flex-1 flex flex-col items-center px-8">
                                <div class="text-center">
                                    <!-- Round -->
                                    <div class="mb-4">
                                        <p class="date-caption text-gray-300 font-medium">
                                            {{ getRoundName(game.round) }}
                                        </p>
                                    </div>
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
                                                    {{ game.team1_goals !== null ? game.team1_goals : '-' }}
                                                </span>
                                            </div>

                                            <div class="flex flex-col items-center">
                                                <span class="text-gray-400 text-sm font-medium mb-1">VS</span>
                                                <div class="w-12 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full"></div>
                                            </div>

                                            <div class="score-box">
                                                <span class="match-score-number">
                                                    {{ game.team2_goals !== null ? game.team2_goals : '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="mt-4">
                                            <span v-if="game.team1_goals !== null"
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
                                        <edit-elimination-game-score
                                            @score-updated="handleScoreUpdate"
                                            :game-id="game.id"
                                            :edited-score="game.team1_goals !== null"
                                            :is-disabled="game.team1_name && game.team2_name ? false : true"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Team 2 -->
                            <div class="flex-1 flex flex-col items-center" :title="game.team2_name">
                                <div class="relative group">
                                    <div class="w-32 h-32 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center border-4 border-gray-600/50 group-hover:border-orange-500/50 transition-all duration-300 shadow-lg">
                                        <img v-if="game.team2_image"
                                             class="object-cover w-28 h-28 rounded-full"
                                             :src="game.team2_image"
                                             alt="Team Image" />
                                        <div v-else class="text-center p-4">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <p class="text-gray-400 text-xs">Nema logo</p>
                                        </div>
                                    </div>
                                    <!-- Glow effect on hover -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-yellow-500/20 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                                <h2 class="team-name mt-6 text-xl font-bold text-white text-center max-w-48 truncate">{{ game.team2_name }}</h2>
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
            <p class="text-gray-500">Trenutno nema eliminacijskih utakmica za ažuriranje.</p>
        </div>
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
    color: #f97316;
    transform: translateY(-2px);
}

.match-score-number {
    font-size: 3rem;
    font-weight: 900;
    color: #ffffff;
    text-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
    transition: all 0.3s ease;
}

.score-box {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(249, 115, 22, 0.1));
    border: 2px solid rgba(239, 68, 68, 0.3);
    border-radius: 16px;
    padding: 1rem 2rem;
    /* backdrop-filter: blur(10px); */
    transition: all 0.3s ease;
}

.score-box:hover {
    border-color: rgba(239, 68, 68, 0.6);
    box-shadow: 0 0 30px rgba(239, 68, 68, 0.3);
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
