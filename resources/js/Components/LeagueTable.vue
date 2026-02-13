<script setup>
import {useTournamentStore} from "@/stores/Tournament.js";

const tournamentStore = useTournamentStore();
const tournamentName = tournamentStore.getName;
const props = defineProps({
    teams: {
        type: Array,
    },
});
const getRowClasses = (index) => {
    return index % 2 === 0 ? 'bg-black bg-opacity-20' : '';
};

</script>

<template>
    <div class="flex flex-col items-center justify-center bg-gray-900 py-4 px-0 sm:py-10 sm:px-4">
        <h1 class="text-lg text-gray-400 font-medium px-2 sm:px-0">{{ tournamentName }} tabelaaaaa</h1>
        <div class="flex flex-col mt-4 sm:mt-6 w-full max-w-7xl mx-auto px-0 sm:px-4">
            <div class="relative overflow-x-auto border-0 sm:border border-gray-800 rounded-none sm:rounded-lg">
                <table class="table-fixed w-full min-w-[640px] sm:min-w-[800px] text-xs sm:text-sm text-gray-400 league-table">
                    <thead class="bg-gray-800 text-[10px] sm:text-xs uppercase font-medium">
                            <tr>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-1 text-center tracking-wider sticky left-0 bg-gray-800 z-10 border-r border-gray-600 md:border-r-0 w-6 rank-header">
                                    #
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider team-name-header">
                                    Ime
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    UT
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    P
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    N
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    I
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    GD
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    GP
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-7 md:w-auto">
                                    GR
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-2 md:px-3 text-center tracking-wider w-8 md:w-auto">
                                    Bod
                                </th>
                            </tr>
                            </thead>
                        <tbody class="bg-gray-800">
                            <tr v-for="(team,index) in props.teams" :key="team.ID" :class="getRowClasses(index)">
                                <td class="py-2 px-0.5 sm:pl-4 sm:py-4 sm:px-2 text-center sticky left-0 bg-gray-800 z-10 border-r border-gray-600 md:border-r-0 rank-cell" :class="getRowClasses(index)">
                                    {{ team.Ranking }}
                                </td>
                                <td class="flex items-center h-full px-2 py-2 sm:px-2 sm:py-4 md:px-4 sticky left-8 sm:left-12 bg-gray-800 z-10 border-r border-gray-600 md:border-r-0 team-name-cell" :class="getRowClasses(index)">
                                    <img v-if=" team.image_path" :src=" team.image_path" alt="Logo tima" class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 flex-shrink-0" />
                                    <img v-else src="https://placehold.co/40x40?text=No+team+logo+set" alt="Logo tima" class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 flex-shrink-0 object-cover">
                                    <span class="ml-1 sm:ml-2 md:ml-3 font-medium flex-1 min-w-0">
                                        <span class="team-name-mobile block sm:hidden truncate">{{ team.shorten_name }}</span>
                                        <span class="team-name-desktop hidden sm:inline-block truncate">{{ team.name }}</span>
                                    </span>
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GamesPlayed }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Wins }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Draws }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Losses }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GoalsScored }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GoalsReceived }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GoalDiff }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-2 md:px-3 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Points }}
                                </td>
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

</template>

<style scoped>
/* Mobile: Keep IME column narrow */
.team-name-header {
    width: 10%;
}

.team-name-cell {
    width: 72px;
    min-width: 72px;
}

/* Mobile: Show mobile version, hide desktop version */
.league-table td.team-name-cell > span > span.team-name-mobile {
    display: block;
}

.league-table td.team-name-cell > span > span.team-name-desktop {
    display: none;
}

@media (min-width: 640px) {
    .team-name-cell {
        width: 96px; /* sm:w-24 */
        min-width: 96px;
    }

    /* Show desktop version, hide mobile version on tablet+ */
    .league-table td.team-name-cell > span > span.team-name-mobile {
        display: none !important;
    }

    .league-table td.team-name-cell > span > span.team-name-desktop {
        display: inline-block !important;
    }
}

/* Desktop and Tablet: Expand IME column, reduce others */
@media (min-width: 768px) {
    .league-table th.team-name-header,
    .league-table td.team-name-cell {
        width: 30% !important;
        min-width: 250px !important;
    }

    /* Remove border on desktop/tablet for rank and team name cells */
    .league-table th.rank-header,
    .league-table td.rank-cell,
    .league-table td.team-name-cell {
        border-right: none !important;
    }

    /* Reduce padding on other columns to compensate */
    .league-table th:not(.team-name-header):not(.rank-header),
    .league-table td:not(.team-name-cell):not(.rank-cell) {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    /* Ensure team name cell has proper flex layout */
    .league-table td.team-name-cell {
        display: flex !important;
        align-items: center;
    }

    /* Ensure team name text is visible */
    .league-table td.team-name-cell > span {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
    }

    /* Show team name text properly - only for desktop version */
    .league-table td.team-name-cell > span > span.team-name-desktop {
        display: inline-block !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /* Hide mobile version on desktop/tablet */
    .league-table td.team-name-cell > span > span.team-name-mobile {
        display: none !important;
    }
}
</style>
