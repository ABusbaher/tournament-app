<script setup>

import {computed, onMounted, ref} from "vue";
import AddTeamForm from "@/Pages/Teams/Partials/AddTeamForm.vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import StatusMessage from "@/Components/StatusMessage.vue";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm.vue";
import EditTeamForm from "@/Pages/Teams/Partials/EditTeamForm.vue";

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

const EditTeamMsg = ref(false);
const showEditTeamMsg = () => {
    EditTeamMsg.value = true;
    setTimeout(closeEditTeamMsg, 5000);
};
const closeEditTeamMsg = () => {
    EditTeamMsg.value = false;
};

const DeleteTeamMsg = ref(false);
const showDeleteTeamMsg = () => {
    DeleteTeamMsg.value = true;
    setTimeout(closeDeleteTeamMsg, 5000);
};
const closeDeleteTeamMsg = () => {
    DeleteTeamMsg.value = false;
};

const handleTeamCreated = () => {
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
    showAddTeamMsg();
};

const handleTeamUpdate = (updatedTournament) => {
    teams.value = teams.value.map(item => {
        if (item.id === updatedTournament.id) {
            Object.assign(item, updatedTournament)
        }
        return item;
    })
    showEditTeamMsg();
};

const handleTeamDelete = () => {
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
    showDeleteTeamMsg();
};

onMounted(() => {
    // const tournamentId = new URLSearchParams(window.location.search).get('page') || 1;
    // const tournamentId = route.path.split('/')[2];
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
});
</script>
<template>
    <StatusMessage message="Team successfully added" color="green" :show="AddTeamMsg"  @close="closeAddTeamMsg"/>
    <StatusMessage message="Tournament name successfully edited" color="green" :show="EditTeamMsg"  @close="closeEditTeamMsg"/>
    <StatusMessage message="Team successfully deleted" color="green" :show="DeleteTeamMsg"  @close="closeDeleteTeamMsg"/>
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
            <td class="py-3 px-4 text-center flex justify-center">
                <template  v-if="team.image_path">
                    <img :src="team.image_path" alt="Team Image" width="100" height="100"/>
                </template>
                <template v-else>
                    No image available
                </template>
            </td>
            <td class="py-3 px-4 text-center">{{ team.id }}</td>
            <td class="py-3 px-4 text-center">
                <edit-team-form @team-updated="handleTeamUpdate" :team-id="team.id" />
                <delete-team-form @team-deleted="handleTeamDelete" :team-id="team.id" />
            </td>
        </tr>
        <tr v-else>
            <td class="text-center" colspan="4">No teams created yet.</td>
        </tr>
        </tbody>
    </table>

</template>
