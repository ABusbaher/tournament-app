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
    <div class="flex flex-col items-center justify-center bg-gray-900 py-10">
        <h1 class="text-lg text-gray-400 font-medium">{{ tournamentName }} table</h1>
        <div class="flex flex-col mt-6">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden sm:rounded-lg">
                        <table class="min-w-full text-sm text-gray-400">
                            <thead class="bg-gray-800 text-xs uppercase font-medium">
                            <tr>
                                <th scope="col" class="py-3 px-1 text-left tracking-wider">
                                    Rank
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    Club
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    MP
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    W
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    D
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    L
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    GF
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    GA
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    GD
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    Pts
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-gray-800">

                            <tr v-for="(team,index) in props.teams" :key="team.ID" :class="getRowClasses(index)">
                                <td class="pl-4">
                                    {{ team.Ranking }}
                                </td>
                                <td class="flex justify-center items-center h-full px-6 py-4 ">
                                    <img v-if=" team.image_path" :src=" team.image_path" alt="Team Image" class="w-10" />
                                    <img v-else src="https://placehold.co/40x40?text=No+team+logo+set" alt="Team Logo">
                                    <span class="ml-2 font-medium text-center">{{ team.team }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.GamesPlayed }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.Wins }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.Draws }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.Losses }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.GoalsScored }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.GoalsReceived }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.GoalDiff }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ team.Points }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="flex flex-col items-center justify-center bg-gray-900 py-10">
        <!-- Component Start -->
        <h1 class="text-lg text-gray-400 font-medium">{{ tournamentName }} table</h1>
        <div class="flex flex-col mt-6">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden sm:rounded-lg">
                        <table class="min-w-full text-sm text-gray-400">
                            <thead class="bg-gray-800 text-xs uppercase font-medium">
                            <tr>
                                <th></th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    Club
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    MP
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    W
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    D
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    L
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    GF
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    GA
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    GD
                                </th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">
                                    Pts
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-gray-800">

                            <tr class="bg-black bg-opacity-20">
                                <td class="pl-4">
                                    1
                                </td>
                                <td class="flex px-6 py-4 whitespace-nowrap">
                                    <img class="w-5" src="https://ssl.gstatic.com/onebox/media/sports/logos/udQ6ns69PctCv143h-GeYw_48x48.png" alt="">
                                    <span class="ml-2 font-medium">Man United</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    17
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    11
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    3
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    3
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    34
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    24
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    10
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    34
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-4">
                                    2
                                </td>
                                <td class="flex px-6 py-4 whitespace-nowrap">
                                    <img class="w-5" src="https://ssl.gstatic.com/onebox/media/sports/logos/0iShHhASp5q1SL4JhtwJiw_48x48.png" alt="">
                                    <span class="ml-2 font-medium">Liverpool</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    17
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    9
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    6
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    2
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    37
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    21
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    16
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    33
                                </td>
                            </tr>
                            <tr class="bg-black bg-opacity-20">
                                <td class="pl-4">
                                    3
                                </td>
                                <td class="flex px-6 py-4 whitespace-nowrap">
                                    <img class="w-5" src="https://ssl.gstatic.com/onebox/media/sports/logos/UDYY4FSlty6fXFBzvFfcyw_48x48.png" alt="">
                                    <span class="ml-2 font-medium">Leicester City</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    17
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    10
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    2
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    5
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    31
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    21
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    10
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    32
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-4">
                                    4
                                </td>
                                <td class="flex px-6 py-4 whitespace-nowrap">
                                    <img class="w-5" src="https://ssl.gstatic.com/onebox/media/sports/logos/C3J47ea36cMBc4XPbp9aaA_48x48.png" alt="">
                                    <span class="ml-2 font-medium">Everton</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    17
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    10
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    2
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    5
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    28
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    21
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    17
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    32
                                </td>
                            </tr>
                            <tr class="bg-black bg-opacity-20">
                                <td class="pl-4">
                                    5
                                </td>
                                <td class="flex px-6 py-4 whitespace-nowrap">
                                    <img class="w-5" src="https://ssl.gstatic.com/onebox/media/sports/logos/k3Q_mKE98Dnohrcea0JFgQ_48x48.png" alt="">
                                    <span class="ml-2 font-medium">Tottenham</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    16
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    18
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    5
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    3
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    29
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    15
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    14
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    29
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-4">
                                    6
                                </td>
                                <td class="flex px-6 py-4 whitespace-nowrap">
                                    <img class="w-5" src="https://ssl.gstatic.com/onebox/media/sports/logos/z44l-a0W1v5FmgPnemV6Xw_48x48.png" alt="">
                                    <span class="ml-2 font-medium">Man. City</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    15
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    8
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    5
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    2
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    24
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    13
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    11
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    29
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Component End  -->

    </div>
</template>
