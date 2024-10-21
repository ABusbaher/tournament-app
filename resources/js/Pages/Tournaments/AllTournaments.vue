<script setup>
import { ref, onMounted } from 'vue';
import AddTournamentForm from "@/Pages/Tournaments/Partials/AddTournamentForm.vue";
import BasePagination from "@/Components/BasePagination.vue";
import EditTournamentNameForm from "@/Pages/Tournaments/Partials/EditTournamentNameForm.vue";
import DeleteTournamentForm from "@/Pages/Tournaments/Partials/DeleteTournamentForm.vue";
import StatusMessage from "@/Components/StatusMessage.vue";

const tournaments = ref([]);
const currentPage = ref(1);
const nextPageLink = ref('');
const previousPageLink = ref('');
const from = ref(1);
const to = ref(1);
const total = ref(1);
const user = window.Laravel?.user;

const fetchTournaments = (url) => {
    if (url !== null) {
        axios.get(url)
            .then(response => {
                tournaments.value = response.data['tournaments'].data;
                currentPage.value = response.data['tournaments'].current_page;
                nextPageLink.value = response.data['tournaments'].next_page_url;
                previousPageLink.value = response.data['tournaments'].prev_page_url;
                from.value = response.data['tournaments'].from;
                to.value = response.data['tournaments'].to;
                total.value = response.data['tournaments'].total;
            })
            .catch(error => {
                console.log(error);
            });
    }
};
const handleTournamentCreated = (newTournament) => {
    // Add the newly created tournament at the beginning of the tournaments list and remove last previous
    // tournaments.value.unshift(newTournament);
    // tournaments.value.pop();
    // go to first page with newly created on top
    fetchTournaments(`/api/tournaments?page=1`);
    showAddTournamentMsg();
};

const handleTournamentUpdate = (updatedTournament) => {
    tournaments.value = tournaments.value.map(item => {
        if (item.id === updatedTournament.id) {
            Object.assign(item, updatedTournament)
        }
        return item;
    })
    showEditTournamentMsg();
};

const handleTournamentDelete = (deletedTournament) => {
    // tournaments.value = tournaments.value.filter(item => item.id !== deletedTournament);
    fetchTournaments(`/api/tournaments?page=${currentPage.value}`)
    showDeleteTournamentMsg();
};


const gameLink = (tournamentType, tournamentId) => {
    return tournamentType === 'league' ? `/tournaments/${tournamentId}/fixtures/1` : `/tournaments/${tournamentId}/elimination`;
};

// Fetch the tournaments when the component is mounted
onMounted(() => {
    const page = new URLSearchParams(window.location.search).get('page') || 1;
    fetchTournaments(`/api/tournaments?page=${page}`)
});


const AddTournamentMsg = ref(false);
const showAddTournamentMsg = () => {
    AddTournamentMsg.value = true;
    setTimeout(closeAddTournamentMsg, 5000);
};
const closeAddTournamentMsg = () => {
    AddTournamentMsg.value = false;
};

const EditTournamentMsg = ref(false);
const showEditTournamentMsg = () => {
    EditTournamentMsg.value = true;
    setTimeout(closeEditTournamentMsg, 5000);
};
const closeEditTournamentMsg = () => {
    EditTournamentMsg.value = false;
};

const DeleteTournamentMsg = ref(false);
const showDeleteTournamentMsg = () => {
    DeleteTournamentMsg.value = true;
    setTimeout(closeDeleteTournamentMsg, 5000);
};
const closeDeleteTournamentMsg = () => {
    DeleteTournamentMsg.value = false;
};
</script>
<template>
    <StatusMessage message="Tournament successfully added" color="green" :show="AddTournamentMsg"  @close="closeAddTournamentMsg"/>
    <StatusMessage message="Tournament name successfully edited" color="green" :show="EditTournamentMsg"  @close="closeEditTournamentMsg"/>
    <StatusMessage message="Tournament successfully deleted" color="green" :show="DeleteTournamentMsg"  @close="closeDeleteTournamentMsg"/>
    <div v-if="user && user.role === 'admin'" class="flex justify-end mb-6">
        <AddTournamentForm @tournamentCreated="handleTournamentCreated"></AddTournamentForm>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
        <tr>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Tournament name</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Tournament type</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Number of rounds</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Games link</th>
            <th v-if="user && user.role === 'admin'" class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Create/Update teams</th>
            <th v-if="user && user.role === 'admin'" class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Action</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="tournament in tournaments" :key="tournament.id">
                <td class="py-3 px-4 text-center">{{ tournament.name }}</td>
                <td class="py-3 px-4 text-center">{{ tournament.type }}</td>
                <td class="py-3 px-4 text-center">{{ tournament.rounds }}</td>
                <td class="py-3 px-4 text-center">
                    <a :href="gameLink(tournament.type, tournament.id)" class="hover:underline hover:font-bold">Visit games page</a>
                </td>
                <td v-if="user && user.role === 'admin'" class="py-3 px-4 text-center">
                    <a :href="`/tournaments/${tournament.id}/teams`" class="hover:underline hover:font-bold">Visit teams page</a></td>
                <td v-if="user && user.role === 'admin'" class="py-3 px-4 text-center">
                    <edit-tournament-name-form @tournamentEdited="handleTournamentUpdate" :tournamentId="tournament.id" />
                    <delete-tournament-form @tournament-deleted="handleTournamentDelete" :tournament-id="tournament.id" />
                </td>
            </tr>
        </tbody>
    </table>

    <base-pagination
        :current-page="currentPage"
        @emitNextPage="fetchTournaments(nextPageLink)"
        @emitPrevPage="fetchTournaments(previousPageLink)"
        :from="from"
        :to="to"
        :total="total"
    />
</template>
