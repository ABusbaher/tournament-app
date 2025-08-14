<template>
    <div id="app" class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <div class="container">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-2 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                    {{ tournamentName }}
                </h1>
                <p class="text-gray-300 text-lg font-medium">Kostur takmičenja</p>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-400 to-purple-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- Tournament Bracket -->
            <div class="tournament-bracket tournament-bracket--rounded relative">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                </div>

                <div v-for="round in rounds" :key="round.number" :class="round.class">
                    <h3 class="tournament-bracket__round-title">{{ round.title }}</h3>
                    <ul class="tournament-bracket__list">
                        <li v-if="maxRound === round.number" v-for="index in nonPlayedGames" :key="index" class="tournament-bracket__item">
                            <div class="tournament-bracket__match" tabindex="0">
                                <table class="tournament-bracket__table">
                                    <caption class="tournament-bracket__caption">
                                        <time datetime="-">-</time>
                                    </caption>
                                    <thead class="sr-only">
                                    <tr>
                                        <th>Tim</th>
                                        <th>Rezultat</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tournament-bracket__content">
                                    <tr class="tournament-bracket__team tournament-bracket__team--winner">
                                        <td class="tournament-bracket__country">
                                            <abbr class="tournament-bracket__code" title="-">-</abbr>
                                            <span class="tournament-bracket__flag flag-icon flag-icon-ca" aria-label="Flag"></span>
                                        </td>
                                        <td class="tournament-bracket__score">
                                            <span class="tournament-bracket__number">-</span>
                                        </td>
                                    </tr>
                                    <tr class="tournament-bracket__team">
                                        <td class="tournament-bracket__country">
                                            <abbr class="tournament-bracket__code" title="-">-</abbr>
                                            <span class="tournament-bracket__flag flag-icon flag-icon-kz" aria-label="Flag"></span>
                                        </td>
                                        <td class="tournament-bracket__score">
                                            <span class="tournament-bracket__number">-</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                        <BracketMatch
                            v-for="game in filterGamesByRound(round.number)"
                            :key="game.id"
                            :game="game"
                            :gameTime="game.date"
                            :isFinal="round.isFinal"
                            :isWinnerTeam1="game.team1_goals > game.team2_goals"
                            :isWinnerTeam2="game.team2_goals > game.team1_goals"
                            :medalType="round.medalType"
                            :medalColor="round.medalColor"
                        />
                    </ul>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="games.length === 0" class="flex justify-center items-center py-12">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400 mx-auto mb-4"></div>
                    <p class="text-gray-400 text-lg">Učitavanje kostura...</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import BracketMatch from "@/Pages/Games/Partials/BracketMatch.vue";
import '@/../css/elemination-braket.css';

const games = ref([]);
const maxRound = ref(0);
const nonPlayedGames = ref(0);
const tournamentStore = useTournamentStore();
tournamentStore.setIdFromUrl();
const tournamentId = tournamentStore.getId;
tournamentStore.getByTournamentById(tournamentId);
const tournamentName = ref(tournamentStore.getName);

const fetchGames = (url) => {
    if (url !== null) {
        axios.get(url)
            .then(response => {
                games.value = response.data.games;
                maxRound.value = response.data.max_round;
                nonPlayedGames.value = response.data.non_played_games;
            })
            .catch(error => {
                console.log(error);
            });
    }
};

onMounted(async() => {
    try {
        await tournamentStore.getByTournamentById(tournamentId);
        tournamentName.value = tournamentStore.getName;
        fetchGames(`/api/tournaments/${tournamentId}/elimination`);
    } catch (error) {
        console.log(error);
    }
});

const rounds = computed(() => {
    const allRounds = [
        { number: 5, title: '1/16 finala', class: 'tournament-bracket__round tournament-bracket__round--round-of-32' },
        { number: 4, title: '1/8 finala', class: 'tournament-bracket__round tournament-bracket__round--round-of-16' },
        { number: 3, title: 'Četvrtfinale', class: 'tournament-bracket__round tournament-bracket__round--quarterfinals' },
        { number: 2, title: 'Polufinale', class: 'tournament-bracket__round tournament-bracket__round--semifinals' },
        // { number: 1, title: 'Meč za treće mesto', class: 'tournament-bracket__round tournament-bracket__round--bronze', isFinal: true, medalType: 'Bronzana medalja', medalColor: '#CD7F32' },
        { number: 1, title: 'Finale', class: 'tournament-bracket__round tournament-bracket__round--gold', isFinal: true, medalType: 'Zlatna medalja', medalColor: '#FFD700' },
    ];
    return allRounds.filter(round => round.number <= maxRound.value);
});
const filterGamesByRound = (round) => {
    return games.value.filter(game => game.round === round);
};

</script>

<style scoped>
/* Enhanced dark theme with modern design */
:deep(.tournament-bracket) {
    position: relative;
    z-index: 1;
}

:deep(.tournament-bracket__round-title) {
    color: #e5e7eb !important;
    font-weight: 700 !important;
    font-size: 1.1rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    margin-bottom: 1.5rem !important;
}

:deep(.tournament-bracket__match) {
    background: linear-gradient(145deg, #1f2937, #111827) !important;
    border: 2px solid #374151 !important;
    border-radius: 12px !important;
    box-shadow:
        0 8px 32px rgba(0, 0, 0, 0.3),
        0 4px 16px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

:deep(.tournament-bracket__match:hover) {
    border-color: #60a5fa !important;
    box-shadow:
        0 12px 40px rgba(96, 165, 250, 0.3),
        0 8px 24px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    //transform: translateY(-2px) !important;
}

:deep(.tournament-bracket__match:focus) {
    outline: none !important;
    border-color: #3b82f6 !important;
    box-shadow:
        0 0 0 3px rgba(59, 130, 246, 0.3),
        0 12px 40px rgba(96, 165, 250, 0.3) !important;
}

:deep(.tournament-bracket__code) {
    color: #f9fafb !important;
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
}

:deep(.tournament-bracket__number) {
    background: linear-gradient(145deg, #374151, #1f2937) !important;
    border: 2px solid #4b5563 !important;
    color: #f9fafb !important;
    font-weight: 700 !important;
    border-radius: 8px !important;
    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
}

:deep(.tournament-bracket__team--winner .tournament-bracket__number) {
    background: linear-gradient(145deg, #059669, #047857) !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
    box-shadow:
        0 4px 12px rgba(16, 185, 129, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    animation: winnerGlow 2s ease-in-out infinite alternate !important;
}

:deep(.tournament-bracket__team--winner .tournament-bracket__code) {
    color: #10b981 !important;
    font-weight: 700 !important;
}

:deep(.tournament-bracket__caption) {
    color: #d1d5db !important;
    font-weight: 500 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
}

:deep(.tournament-bracket__medal) {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
    animation: medalShine 3s ease-in-out infinite !important;
}

/* Enhanced scrollbar */
::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

::-webkit-scrollbar-track {
    background: linear-gradient(145deg, #1f2937, #111827);
    border-radius: 6px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(145deg, #4b5563, #374151);
    border-radius: 6px;
    border: 2px solid #1f2937;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(145deg, #60a5fa, #3b82f6);
}

::-webkit-scrollbar-corner {
    background: #1f2937;
}

/* Animations */
@keyframes winnerGlow {
    0% {
        box-shadow:
            0 4px 12px rgba(16, 185, 129, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    }
    100% {
        box-shadow:
            0 6px 20px rgba(16, 185, 129, 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
    }
}

@keyframes medalShine {
    0%, 100% {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
    }
    50% {
        filter: drop-shadow(0 4px 8px rgba(255, 215, 0, 0.5)) !important;
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    :deep(.tournament-bracket__match) {
        margin: 0.5rem 0 !important;
    }

    :deep(.tournament-bracket__round-title) {
        font-size: 0.9rem !important;
        margin-bottom: 1rem !important;
    }
}

/* Smooth transitions for all interactive elements */
:deep(*) {
    transition: all 0.2s ease-in-out;
}
</style>


