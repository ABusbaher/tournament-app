<script setup>
import {onMounted, reactive, ref, toRefs} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import EditGameScore from "@/Pages/Games/Partials/EditEliminationGameScore.vue";
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
    fetchGames(`/api/tournaments/${tournamentId}/elimination`);
    showMessage("updateGameScore");
}

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
    fetchGames(`/api/tournaments/${tournamentId}/elimination`);
});

</script>
<template>
    <div class="bg-white min-h-screen">
        <h1 class="mb-4 text-4xl font-extrabold text-center leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
            Ažuriraj utakmice turnira {{ tournamentName }}
        </h1>
        <StatusMessage message="Rezultat utakmice uspešno izmenjen" color="green" :show="messages.updateGameScore"
                       @close="messages.updateGameScore = false"/>
        <div v-if="games.length" v-for="game in games" :key="game.id" class="mb-6">
            <h2 v-if="game.team1_name || game.team2_name" class="team-name mt-4">{{ getRoundName(game.round) }}</h2>
            <div v-if="game.team1_name || game.team2_name" class="match bg-white rounded-lg shadow-md flex items-center justify-center">
                <div class="match-content flex flex-col md:flex-row">
                    <div class="column p-3 flex justify-center items-center" :title="game.team1_name">
                        <div class="team flex flex-col items-center">
                            <div class="team-logo w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                <img v-if="game.team1_image" :src="game.team1_image" alt="Team Image" />
                                <p v-else class="text-center p-3">Tim nema postavljen logo</p>
                            </div>
                            <h2 class="team-name mt-4">{{ game.team1_shorten_name || 'Još nema tima' }}</h2>
                        </div>
                    </div>

                    <div class="column p-3 flex justify-center">
                        <div class="match-details text-center">
                            <div>
                                <p class="date-caption">
                                    {{ game.game_time !== null ? formatDate(new Date(game.game_time)) : 'Vreme utakmice nije još postavljeno' }}
                                </p>
                            </div>
                            <div class="match-score flex items-center justify-center mt-2">
                                <span class="match-score-number text-5xl font-bold">
                                    {{ game.team1_goals !== null ? game.team1_goals : 'v' }}
                                </span>
                                <span v-if="game.host_goals !== null" class="match-score-divider text-gray-400 mx-2">:</span>
                                <span class="match-score-number text-5xl font-bold">
                                    {{ game.team2_goals !== null ? game.team2_goals : 's' }}
                                </span>
                            </div>
                            <edit-game-score
                                @score-updated="handleScoreUpdate"
                                :game-id="game.id"
                                :edited-score="game.team1_goals !== null"
                                :is-disabled="game.team1_name && game.team2_name ? false : true"
                            />
                        </div>
                    </div>
                    <div class="column p-3 flex justify-center items-center" :title="game.team2_name">
                        <div class="team flex flex-col items-center">
                            <div class="team-logo w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                <img v-if="game.team2_image" :src="game.team2_image" alt="Team Image" />
                                <p v-else class="text-center p-3">Tim nema postavljen logo</p>
                            </div>
                            <h2 class="team-name mt-4">{{ game.team2_shorten_name || 'Još nema tima' }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.date-caption {
    font-size: 1.1rem;
    color: #313131;
    font-weight: 650;
}

@media (max-width: 40em) {
    h2 {
        margin-bottom: 0;
    }
}
</style>
