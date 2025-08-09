<template>
    <li class="tournament-bracket__item">
        <div class="tournament-bracket__match" tabindex="0">
            <table class="tournament-bracket__table">
                <caption class="tournament-bracket__caption">
                    <time :datetime="gameTime" class="game-time">
                        {{ game.game_time !== null ? formatDate(new Date(game.game_time)) : '-' }}
                    </time>
                </caption>
                <thead class="sr-only">
                <tr>
                    <th>Tim</th>
                    <th>Rezultat</th>
                </tr>
                </thead>
                <tbody class="tournament-bracket__content">
                <tr class="tournament-bracket__team" :class="{ 'tournament-bracket__team--winner': isWinnerTeam1 }">
                    <td class="tournament-bracket__country">
                        <abbr class="tournament-bracket__code" :title="game.team1_name">{{ game.team1_shorten_name }}</abbr>
                        <span class="tournament-bracket__flag" aria-label="Flag">
                            <img v-if="game.team1_image" class="team-image" :src="game.team1_image" alt="Team 1 Image" />
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
                            <img v-if="game.team2_image" class="team-image" :src="game.team2_image" alt="Team 2 Image" />
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
/* Enhanced styling for BracketMatch component */
.game-time {
    font-size: 0.85rem !important;
    font-weight: 500 !important;
    color: #9ca3af !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
}

.team-image {
    width: 2rem !important;
    height: 2rem !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 2px solid #374151 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    transition: all 0.3s ease !important;
}

.team-image:hover {
    transform: scale(1.1) !important;
    border-color: #60a5fa !important;
    box-shadow: 0 4px 8px rgba(96, 165, 250, 0.4) !important;
}

/* Enhanced winner styling */
:deep(.tournament-bracket__team--winner .team-image) {
    border-color: #10b981 !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4) !important;
}

:deep(.tournament-bracket__team--winner .team-image:hover) {
    border-color: #34d399 !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.6) !important;
}

/* Enhanced medal styling */
:deep(.tournament-bracket__medal) {
    margin-left: 0.5rem !important;
    font-size: 1.2rem !important;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
    animation: medalShine 3s ease-in-out infinite !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .team-image {
        width: 1.5rem !important;
        height: 1.5rem !important;
    }
    
    .game-time {
        font-size: 0.75rem !important;
    }
    
    :deep(.tournament-bracket__medal) {
        font-size: 1rem !important;
        margin-left: 0.25rem !important;
    }
}

/* Smooth transitions */
:deep(.tournament-bracket__team) {
    transition: all 0.3s ease !important;
}

:deep(.tournament-bracket__team:hover) {
    background: rgba(96, 165, 250, 0.1) !important;
}

:deep(.tournament-bracket__team--winner:hover) {
    background: rgba(16, 185, 129, 0.1) !important;
}
</style>
