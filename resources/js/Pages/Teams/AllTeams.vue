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
const user = window.Laravel?.user;
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
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <!-- Modern Header -->
        <div class="text-center mb-12 pt-8">
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 rounded-full mb-6">
                <span class="text-white font-semibold text-sm uppercase tracking-wider">Timovi</span>
            </div>
            <div class="relative">
                <!-- Darker background for better contrast -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                        Timovi
                    </h1>
                    <p class="text-gray-300 mt-4 text-lg font-medium">Upravljajte timovima u turniru</p>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <StatusMessage message="Turnir uspešno izmenjen" color="green" :show="messages.editTournament"
                       @close="messages.editTournament = false"/>
        <StatusMessage message="Tim uspešno dodat" color="green"  :show="messages.addTeam"
                       @close="messages.addTeam = false"/>
        <StatusMessage message="Tim uspešno izmenjen" color="green" :show="messages.editTeam"
                       @close="messages.editTeam = false"/>
        <StatusMessage message="Tim uspešno obrisan" color="green" :show="messages.deleteTeam"
                       @close="messages.deleteTeam = false"/>
        <StatusMessage message="Raspored uspešno kreiran" color="green" :show="messages.createFixtures"
                       @close="messages.createFixtures = false"/>

        <!-- Admin Add Button -->
        <div v-if="user && user.role === 'admin'" class="flex justify-end mb-8">
            <AddTeamForm @teamCreated="handleTeamCreated"/>
        </div>

        <!-- Teams Table Section -->
        <div class="mb-12">
            <div class="bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-600/50">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Ime</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <span>Skraćeno ime</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Logo</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                        </svg>
                                        <span>Akcija</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800/50 divide-y divide-gray-600/30">
                            <tr v-if="teams.length" v-for="(team, index) in teams" :key="team.id" 
                                class="hover:bg-gray-700/30 transition-colors duration-200 transform transition-all duration-500"
                                :style="{ animationDelay: `${index * 100}ms` }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-green-600 to-emerald-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ team.shorten_name.charAt(0) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ team.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        {{ team.shorten_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex justify-center">
                                        <template v-if="team.image_path">
                                            <img :src="team.image_path" alt="Logo tima" 
                                                 class="h-16 w-16 rounded-lg object-cover border-2 border-gray-600/50 shadow-lg"/>
                                        </template>
                                        <template v-else>
                                            <div class="h-16 w-16 rounded-lg bg-gray-700/50 border-2 border-dashed border-gray-600/50 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <EditTeamForm @team-updated="handleTeamUpdate" :team-id="team.id" />
                                        <DeleteTeamForm @team-deleted="handleTeamDelete" :team-id="team.id" />
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="w-16 h-16 rounded-full bg-gray-700/50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-300">Još nema kreiranih timova</h3>
                                            <p class="text-gray-500">Dodajte prvi tim da počnete</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tournament Management Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Edit Tournament Form -->
            <div class="bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 overflow-hidden">
                <div class="p-6 border-b border-gray-600/30">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Izmeni Turnir</h3>
                            <p class="text-gray-400 text-sm">Ažurirajte informacije o turniru</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <EditTournamentForm @tournament-edited="handleTournamentUpdate" :tournament-id="parseInt(tournamentId.value)"/>
                </div>
            </div>

            <!-- Create Fixtures -->
            <div class="bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 overflow-hidden">
                <div class="p-6 border-b border-gray-600/30">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Kreiraj Raspored</h3>
                            <p class="text-gray-400 text-sm">Generišite raspored utakmica</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <CreateFixtures @fixtures-created="handleFixturesCreated"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for table */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Table hover effects */
tbody tr:hover {
    background: rgba(55, 65, 81, 0.3);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Animation for table rows */
tbody tr {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Gradient text animation */
h1 {
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}
</style>
