<script setup>
import { ref, onMounted } from 'vue';
import AddTournamentForm from "@/Pages/Tournaments/Partials/AddTournamentForm.vue";
import BasePagination from "@/Components/BasePagination.vue";
import EditTournamentForm from "@/Pages/Tournaments/Partials/EditTournamentForm.vue";
import DeleteTournamentForm from "@/Pages/Tournaments/Partials/DeleteTournamentForm.vue";
import StatusMessage from "@/Components/StatusMessage.vue";

const tournaments = ref([]);
const currentPage = ref(1);
const nextPageLink = ref('');
const previousPageLink = ref('');
const from = ref(1);
const to = ref(1);
const total = ref(1);

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
    fetchTournaments(`api/tournaments?page=1`);
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
    fetchTournaments(`api/tournaments?page=${currentPage.value}`)
    showDeleteTournamentMsg();
};


// Fetch the tournaments when the component is mounted
onMounted(() => {
    const page = new URLSearchParams(window.location.search).get('page') || 1;
    fetchTournaments(`api/tournaments?page=${page}`)
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
    <div class="flex justify-end mb-6">
        <AddTournamentForm @tournamentCreated="handleTournamentCreated"></AddTournamentForm>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
        <tr>
            <th scope="col" class="py-3 px-4 bg-gray-100 font-medium text-gray-700">
                <div class="flex items-center">
                    <input id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                </div>
            </th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Tournament name</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Tournament type</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Number of rounds</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Link</th>
            <th class="py-3 px-4 bg-gray-100 font-medium text-gray-600">Action</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="tournament in tournaments" :key="tournament.id">
                <td class="w-4 p-4">
                    <div class="flex items-center">
                        <input :id="'checkbox-table-search-' + tournament.id" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label :for="'checkbox-table-search-' + tournament.id" class="sr-only">checkbox</label>
                    </div>
                </td>
                <td class="py-3 px-4 text-center">{{ tournament.name }}</td>
                <td class="py-3 px-4 text-center">{{ tournament.type }}</td>
                <td class="py-3 px-4 text-center">{{ tournament.rounds }}</td>
                <td class="py-3 px-4 text-center">{{ tournament.id }}</td>
                <td class="py-3 px-4 text-center">
                    <edit-tournament-form @tournamentEdited="handleTournamentUpdate" :tournamentId="tournament.id" />
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
