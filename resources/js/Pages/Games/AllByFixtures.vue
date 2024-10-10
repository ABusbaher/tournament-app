<script setup>
import {onMounted, reactive, ref, toRefs} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import BasePagination from "@/Components/BasePagination.vue";
import EditGameScore from "@/Pages/Games/Partials/EditFixtureGameScore.vue";
import StatusMessage from "@/Components/StatusMessage.vue";
import AppTabs from "@/Components/AppTabs.vue";
import LeagueTable from "@/Components/LeagueTable.vue";
import {useDateTimeFormatter} from "@/composables/useDateTimeFormatter.js";

const props = defineProps({
    fixtureId: {
        type: String,
        required: true,
    },
});
const games = ref([]);
const tournamentStore = useTournamentStore();
tournamentStore.setIdFromUrl();
const tournamentId = tournamentStore.getId;
const currentPage = ref(1);
const nextPageLink = ref('');
const previousPageLink = ref('');
const total = ref(1);
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
    fetchGames(`/api/tournaments/${tournamentId}/fixtures/${fixtureId.value}`);
});


const tabList = ["Fixtures", "Table"];

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
    <div class="bg-white min-h-screen">

        <app-tabs class="w-11/12 lg:w-10/12 mx-auto mb-16" :tabList="tabList" @handle-click-second-tab="fetchTable">
            <template v-slot:tabPanel-1>
                <h1 class="mb-4 text-4xl font-extrabold text-center leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                    Games in Fixture {{ currentPage }}
                </h1>
                <StatusMessage message="Game score successfully edited" color="green" :show="messages.updateGameScore"
                               @close="messages.updateGameScore = false"/>
                <div v-if="games.length" v-for="game in games" :key="game.id" class="mb-6">
                    <div class="match bg-white rounded-lg shadow-md flex items-center justify-center">
                        <div class="match-content flex flex-col md:flex-row">
                            <div class="column p-3 flex justify-center items-center" :title="game.host_team_name">
                                <div class="team flex flex-col items-center">
                                    <div class="team-logo w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                        <img v-if="game.host_team_image" :src="game.host_team_image" alt="Team Image" />
                                        <p v-else class="text-center p-3">Team has no logo sett</p>
                                    </div>
                                    <h2 class="team-name mt-4">{{ game.host_team_shortname }}</h2>
                                </div>
                            </div>

                            <div v-if="game.guest_team_name" class="column p-3 flex justify-center">
                                <div class="match-details text-center">
                                    <div>
                                        <p class="date-caption">
                                            {{ game.game_time !== null ? formatDate(new Date(game.game_time)) : 'Game time not set yet' }}
                                        </p>
                                    </div>
                                    <div class="match-score flex items-center justify-center mt-2">
                                        <span class="match-score-number text-5xl font-bold">
                                            {{ game.host_goals !== null ? game.host_goals : 'v' }}
                                        </span>
                                        <span v-if="game.host_goals !== null" class="match-score-divider text-gray-400 mx-2">:</span>
                                        <span class="match-score-number text-5xl font-bold">
                                            {{ game.guest_goals !== null ? game.guest_goals : 's' }}
                                        </span>
                                    </div>
                                    <edit-game-score @score-updated="handleScoreUpdate" :game-id="game.id" :edited-score="game.host_goals !== null" />
                                </div>
                            </div>
                            <div class="column p-3 flex justify-center items-center" v-else>
                                <span class="match-score-number text-5xl font-bold">
                                    free team
                                </span>
                            </div>
                            <div v-if="game.guest_team_name" class="column p-3 flex justify-center items-center" :title="game.guest_team_name">
                                <div class="team flex flex-col items-center">
                                    <div class="team-logo w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                        <img v-if="game.guest_team_image" :src="game.guest_team_image" alt="Team Image" />
                                        <p v-else class="text-center p-3">Team has no logo sett</p>
                                    </div>
                                    <h2 class="team-name mt-4">{{ game.guest_team_shortname }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <base-pagination
                    :current-page="currentPage"
                    @emitNextPage="fetchGames(nextPageLink)"
                    @emitPrevPage="fetchGames(previousPageLink)"
                    :total="total"
                />
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
    color: #313131;
    font-weight: 650;
}

@media (max-width: 40em) {
    h2 {
        margin-bottom: 0;
    }
}
</style>
