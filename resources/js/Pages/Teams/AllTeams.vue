<script setup>

import {onMounted, ref } from "vue";
import AddTeamForm from "@/Pages/Teams/Partials/AddTeamForm.vue";

const teams = ref([]);


const fetchTeams = (url) => {
   axios.get(url)
       .then(response => {
           console.log(response.data)
           teams.value = response.data;
       })
       .catch(error => {
           console.log(error);
       });
};

onMounted(() => {
    // const tournamentId = new URLSearchParams(window.location.search).get('page') || 1;
    // const tournamentId = route.path.split('/')[2];
    const tournamentId = window.location.href.split('/')[4];
    fetchTeams(`/api/tournaments/${tournamentId}/teams`)
});
</script>
<template>
    <div class="flex justify-end mb-6">
        <add-team-form></add-team-form>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
        <tr>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Team name</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Team image</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Link</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="teams.length" v-for="team in teams" :key="team.id">
            <td class="py-3 px-4 text-center">{{ team.name }}</td>
            <td class="py-3 px-4 text-center">{{ team.image_path }}</td>
            <td class="py-3 px-4 text-center">{{ team.id }}</td>
            <td class="py-3 px-4 text-center">
<!--                <edit-tournament-form @tournamentEdited="handleTournamentUpdate" :tournamentId="team.id" />-->
<!--                <delete-tournament-form @tournament-deleted="handleTournamentDelete" :tournament-id="team.id" />-->
            </td>
        </tr>
        <tr v-else>
            <td class="text-center" colspan="4">No teams created yet.</td>
        </tr>
        </tbody>
    </table>
</template>
