<template>
    <div id="app" class="container mx-auto p-6 bg-gray-50">
        <h1>{{ tournamentName }} Bracket</h1>
        <div class="tournament-bracket tournament-bracket--rounded">

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
                                    <th>Country</th>
                                    <th>Score</th>
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
        { number: 5, title: 'Round of 32', class: 'tournament-bracket__round tournament-bracket__round--round-of-32' },
        { number: 4, title: 'Round of 16', class: 'tournament-bracket__round tournament-bracket__round--round-of-16' },
        { number: 3, title: 'Quarterfinals', class: 'tournament-bracket__round tournament-bracket__round--quarterfinals' },
        { number: 2, title: 'Semifinals', class: 'tournament-bracket__round tournament-bracket__round--semifinals' },
        // { number: 1, title: 'Bronze medal game', class: 'tournament-bracket__round tournament-bracket__round--bronze', isFinal: true, medalType: 'Bronze medal', medalColor: '#CD7F32' },
        { number: 1, title: 'Gold medal game', class: 'tournament-bracket__round tournament-bracket__round--gold', isFinal: true, medalType: 'Gold medal', medalColor: '#FFD700' },
    ];
    return allRounds.filter(round => round.number <= maxRound.value);
});
const filterGamesByRound = (round) => {
    return games.value.filter(game => game.round === round);
};

</script>


