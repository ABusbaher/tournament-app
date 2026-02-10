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
        <h1 class="text-lg text-gray-400 font-medium px-2 sm:px-0">{{ tournamentName }} tabela</h1>
        <div class="flex flex-col mt-4 sm:mt-6 w-full max-w-7xl mx-auto px-0 sm:px-4">
            <div class="relative overflow-x-auto border-0 sm:border border-gray-800 rounded-none sm:rounded-lg">
                <table class="table-fixed w-full min-w-[640px] sm:min-w-[800px] text-xs sm:text-sm text-gray-400">
                    <thead class="bg-gray-800 text-[10px] sm:text-xs uppercase font-medium">
                            <tr>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-1 text-center tracking-wider sticky left-0 bg-gray-800 z-10 border-r border-gray-600 w-6">
                                    #
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-[10%]" style="width: 10%;">
                                    Ime
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    UT
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    P
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    N
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    I
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    GR
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    GD
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-7">
                                    GP
                                </th>
                                <th scope="col" class="py-2 px-0.5 sm:py-3 sm:px-6 text-center tracking-wider w-8">
                                    Bod
                                </th>
                            </tr>
                            </thead>
                        <tbody class="bg-gray-800">
                            <tr v-for="(team,index) in props.teams" :key="team.ID" :class="getRowClasses(index)">
                                <td class="py-2 px-0.5 sm:pl-4 sm:py-4 sm:px-2 text-center sticky left-0 bg-gray-800 z-10 border-r border-gray-600" :class="getRowClasses(index)">
                                    {{ team.Ranking }}
                                </td>
                                <td class="flex items-center h-full px-2 py-2 sm:px-2 sm:py-4 sticky left-8 sm:left-12 bg-gray-800 z-10 border-r border-gray-600 w-[72px] sm:w-24 min-w-0 box-border" :class="getRowClasses(index)">
                                    <img v-if=" team.image_path" :src=" team.image_path" alt="Logo tima" class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 flex-shrink-0" />
                                    <img v-else src="https://placehold.co/40x40?text=No+team+logo+set" alt="Logo tima" class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 flex-shrink-0 object-cover">
                                    <span class="ml-1 sm:ml-2 font-medium truncate min-w-0">{{ team.team }}</span>
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GamesPlayed }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Wins }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Draws }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Losses }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GoalsScored }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GoalsReceived }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.GoalDiff }}
                                </td>
                                <td class="py-2 px-0.5 sm:px-6 sm:py-4 whitespace-nowrap text-center">
                                    {{ team.Points }}
                                </td>
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

</template>
