<script setup>

import {computed, onMounted, ref} from "vue";
import AddTeamForm from "@/Pages/Teams/Partials/AddTeamForm.vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import StatusMessage from "@/Components/StatusMessage.vue";

const teams = ref([]);
const tournamentStore = useTournamentStore();
tournamentStore.setIdFromUrl();
const tournamentId = tournamentStore.getId;

const fetchTeams = (url) => {
   axios.get(url)
       .then(response => {
           teams.value = response.data;
       })
       .catch(error => {
           console.log(error);
       });
};
const AddTeamMsg = ref(false);
const showAddTeamMsg = () => {
    AddTeamMsg.value = true;
    setTimeout(closeAddTeamMsg, 5000);
};

const closeAddTeamMsg = () => {
    AddTeamMsg.value = false;
};
const handleTeamCreated = () => {
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
    showAddTeamMsg();
};

const baseUrl = computed(() => import.meta.env.BASE_URL);

function getTeamImageUrl(imagePath) {
    return imagePath ? 'http://localhost:8001/storage' + '/' + imagePath : null;
}

onMounted(() => {
    // const tournamentId = new URLSearchParams(window.location.search).get('page') || 1;
    // const tournamentId = route.path.split('/')[2];
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
    console.log(baseUrl.value);
});
</script>
<template>
    <StatusMessage message="Team successfully added" color="green" :show="AddTeamMsg"  @close="closeAddTeamMsg"/>
    <div class="flex justify-end mb-6">
        <add-team-form @teamCreated="handleTeamCreated"/>
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
            <td class="py-3 px-4 text-center">
                <template v-if="team.image_path">
                    <img :src="team.image_path" alt="Team Image" width="100" height="100"/>
<!--                    <img src="http://localhost:8001/storage/team_images/1687177617_Image_20230602_154009_804.jpeg" alt="Team Image" width="100" height="100"/>-->
                </template>
                <template v-else>
                    No image available
                </template>
            </td>
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
