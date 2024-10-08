<template>
    <li class="tournament-bracket__item">
        <div class="tournament-bracket__match" tabindex="0">
            <table class="tournament-bracket__table">
                <caption class="tournament-bracket__caption">
                    <time :datetime="gameTime">
                        {{ game.game_time !== null ? formatDate(new Date(game.game_time)) : 'Game time not set yet' }}
                    </time>
                </caption>
                <thead class="sr-only">
                <tr>
                    <th>Country</th>
                    <th>Score</th>
                </tr>
                </thead>
                <tbody class="tournament-bracket__content">
                <tr class="tournament-bracket__team" :class="{ 'tournament-bracket__team--winner': isWinnerTeam1 }">
                    <td class="tournament-bracket__country">
                        <abbr class="tournament-bracket__code" :title="game.team1_name">{{ game.team1_shorten_name }}</abbr>
                        <span class="tournament-bracket__flag" aria-label="Flag">
                            <img v-if="game.team1_image" :src="game.team1_image" alt="Team 1 Image" />
                        </span>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ game.team1_goals || '-' }}</span>
                        <span v-if="isFinal && isWinnerTeam1" class="tournament-bracket__medal" :aria-label="medalType">
                            <font-awesome-icon :icon="['fas', 'trophy']" :style="{ color: medalColor }" />
                        </span>
                    </td>
                </tr>
                <tr class="tournament-bracket__team" :class="{ 'tournament-bracket__team--winner': isWinnerTeam2 }">
                    <td class="tournament-bracket__country">
                        <abbr class="tournament-bracket__code" :title="game.team2_name">{{ game.team2_shorten_name }}</abbr>
                        <span class="tournament-bracket__flag" aria-label="Flag">
                            <img v-if="game.team2_image" :src="game.team2_image" alt="Team 2 Image" />
                        </span>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ game.team2_goals || '-' }}</span>
                        <span v-if="isFinal && isWinnerTeam2" class="tournament-bracket__medal" :aria-label="medalType">
                            <font-awesome-icon :icon="['fas', 'trophy']" :style="{ color: medalColor }" />
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </li>
</template>

<script setup>
import { computed } from 'vue';
import '@/../css/elemination-braket.css';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {useDateTimeFormatter} from "@/composables/useDateTimeFormatter.js";

// Props
const props = defineProps({
    game: Object,
    gameTime: {
        type: String,
        default: '-'
    },
    isFinal: {
        type: Boolean,
        default: false
    },
    isWinnerTeam1: {
        type: Boolean,
        default: false
    },
    isWinnerTeam2: {
        type: Boolean,
        default: false
    },
    medalType: {
        type: String,
        default: 'Medal'
    },
    medalColor: {
        type: String,
        default: '#FFD700'
    }
});

const { formatDate } = useDateTimeFormatter();
</script>

<style scoped>
/* You can place specific scoped CSS for this component here */
</style>
