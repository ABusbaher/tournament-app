<script setup>
import {onMounted, reactive, ref} from "vue";
import AddTeamForm from "@/Pages/Teams/Partials/AddTeamForm.vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import StatusMessage from "@/Components/StatusMessage.vue";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm.vue";
import EditTeamForm from "@/Pages/Teams/Partials/EditTeamForm.vue";
import EditTournamentForm from "@/Pages/Tournaments/Partials/EditTournamentForm.vue";
import CreateFixtures from "@/Pages/Tournaments/Partials/CreateFixtures.vue";

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
const messages = reactive({
    addTeam: false,
    editTeam: false,
    editTournament: false,
    deleteTeam: false,
    createFixtures: false,
});

const showMessage = (type) => {
    messages[type] = true;
    setTimeout(() => {
        messages[type] = false;
    }, 5000);
};

const handleTeamCreated = () => {
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
    showMessage("addTeam");
};

const handleTeamUpdate = (updatedTournament) => {
    teams.value = teams.value.map((item) => {
        if (item.id === updatedTournament.id) {
            Object.assign(item, updatedTournament);
        }
        return item;
    });
    showMessage("editTeam");
};

const handleTournamentUpdate = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth", // Optional: Smooth scrolling animation
    });
    showMessage("editTournament");
};

const handleTeamDelete = () => {
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
    showMessage("deleteTeam");
};

const handleFixturesCreated = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth", // Optional: Smooth scrolling animation
    });
    showMessage("createFixtures");
}

onMounted(async() => {
    fetchTeams(`/api/tournaments/${tournamentId}/teams`);
});
</script>
<template>
    <StatusMessage message="Tournament successfully edited" color="green" :show="messages.editTournament"
                   @close="messages.editTournament = false"/>
    <StatusMessage message="Team successfully added" color="green"  :show="messages.addTeam"
                   @close="messages.addTeam = false"/>
    <StatusMessage message="Team successfully edited" color="green" :show="messages.editTeam"
                   @close="messages.editTeam = false"/>
    <StatusMessage message="Team successfully deleted" color="green" :show="messages.deleteTeam"
                   @close="messages.deleteTeam = false"/>
    <StatusMessage message="Fixtures successfully created" color="green" :show="messages.createFixtures"
                   @close="messages.createFixtures = false"/>
    <div class="flex justify-end mb-6">
        <add-team-form @teamCreated="handleTeamCreated"/>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
        <tr>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Name</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Short name</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Image</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="teams.length" v-for="team in teams" :key="team.id">
            <td class="py-3 px-4 text-center">{{ team.name }}</td>
            <td class="py-3 px-4 text-center">{{ team.shorten_name }}</td>
            <td class="py-3 px-4 text-center flex justify-center">
                <template  v-if="team.image_path">
                    <img :src="team.image_path" alt="Team Image" width="100" height="100"/>
                </template>
                <template v-else>
                    No image available
                </template>
            </td>
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

    <edit-tournament-form @tournament-edited="handleTournamentUpdate" :tournament-id="parseInt(tournamentId.value)"/>
    <create-fixtures @fixtures-created="handleFixturesCreated"/>

</template>
