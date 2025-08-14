<script setup>
import { ref, onMounted } from 'vue';
import AddTournamentForm from "@/Pages/Tournaments/Partials/AddTournamentForm.vue";
import BasePagination from "@/Components/BasePagination.vue";
import EditTournamentNameForm from "@/Pages/Tournaments/Partials/EditTournamentNameForm.vue";
import DeleteTournamentForm from "@/Pages/Tournaments/Partials/DeleteTournamentForm.vue";
import StatusMessage from "@/Components/StatusMessage.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

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

const getTypeLabel = (type) => {
    switch (type) {
        case 'league':
            return 'liga';
        case 'elimination':
            return 'kup';
        // case 'championship':
        //     return 'lš';
        default:
            return type;
    }
}

const getTypeColor = (type) => {
    switch (type) {
        case 'league':
            return 'from-blue-600 to-purple-600';
        case 'elimination':
            return 'from-purple-600 to-pink-600';
        default:
            return 'from-gray-600 to-gray-700';
    }
}

const getTypeIcon = (type) => {
    switch (type) {
        case 'league':
            return 'futbol';
        case 'elimination':
            return 'trophy';
        default:
            return 'futbol';
    }
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <!-- Modern Header -->
        <div class="text-center mb-12 pt-8">
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-cyan-600 rounded-full mb-6">
                <span class="text-white font-semibold text-sm uppercase tracking-wider">Turniri</span>
            </div>
            <div class="relative">
                <!-- Darker background for better contrast -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl blur-sm"></div>
                <div class="relative bg-gradient-to-br from-gray-800/90 via-gray-700/90 to-gray-900/90 rounded-3xl p-8 border border-gray-600/30 backdrop-blur-sm">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-100 to-gray-200 leading-none tracking-tight drop-shadow-lg">
                        Turniri
                    </h1>
                    <p class="text-gray-300 mt-4 text-lg font-medium">Upravljajte svim turnirima i takmičenjima</p>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <StatusMessage message="Tournament successfully added" color="green" :show="AddTournamentMsg" @close="closeAddTournamentMsg"/>
        <StatusMessage message="Tournament name successfully edited" color="green" :show="EditTournamentMsg" @close="closeEditTournamentMsg"/>
        <StatusMessage message="Tournament successfully deleted" color="green" :show="DeleteTournamentMsg" @close="closeDeleteTournamentMsg"/>

        <!-- Admin Add Button -->
        <div v-if="user && user.role === 'admin'" class="flex justify-end mb-8">
            <AddTournamentForm @tournamentCreated="handleTournamentCreated"></AddTournamentForm>
        </div>

        <!-- Tournaments Grid -->
        <div v-if="tournaments.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div v-for="(tournament, index) in tournaments" :key="tournament.id" 
                 class="transform transition-all duration-500 hover:scale-105"
                 :style="{ animationDelay: `${index * 100}ms` }">
                
                <!-- Tournament Card -->
                <div class="relative overflow-hidden bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 via-cyan-500/10 to-blue-500/10 animate-pulse"></div>
                    
                    <div class="relative p-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div :class="`w-12 h-12 bg-gradient-to-r ${getTypeColor(tournament.type)} rounded-lg flex items-center justify-center`">
                                    <font-awesome-icon class="w-6 h-6 text-white" :icon="['fas', getTypeIcon(tournament.type)]" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ tournament.name }}</h3>
                                    <span :class="`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r ${getTypeColor(tournament.type)} text-white`">
                                        {{ getTypeLabel(tournament.type) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="space-y-4">
                            <!-- Rounds Info -->
                            <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                                <span class="text-gray-300 text-sm">Broj rundi:</span>
                                <span class="text-white font-semibold">{{ tournament.rounds }}</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <!-- View Matches Button -->
                                <a :href="gameLink(tournament.type, tournament.id)" 
                                   class="block w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Pogledaj utakmice</span>
                                </a>

                                <!-- Admin Actions -->
                                <div v-if="user && user.role === 'admin'" class="space-y-2">
                                    <!-- Teams Button -->
                                    <a :href="`/tournaments/${tournament.id}/teams`" 
                                       class="block w-full px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-green-500/30 hover:border-green-400/50 flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span>Upravljaj timovima</span>
                                    </a>

                                    <!-- Edit/Delete Actions -->
                                    <div class="flex space-x-2">
                                        <edit-tournament-name-form @tournamentEdited="handleTournamentUpdate" :tournamentId="tournament.id" />
                                        <delete-tournament-form @tournament-deleted="handleTournamentDelete" :tournament-id="tournament.id" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
            <div class="w-24 h-24 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-300 mb-2">Nema turnira</h3>
            <p class="text-gray-500">Trenutno nema kreiranih turnira.</p>
        </div>

        <!-- Pagination -->
        <div class="mt-12 mb-8">
            <base-pagination
                :current-page="currentPage"
                @emitNextPage="fetchTournaments(nextPageLink)"
                @emitPrevPage="fetchTournaments(previousPageLink)"
                :from="from"
                :to="to"
                :total="total"
            />
        </div>
    </div>
</template>

<style scoped>
/* Animation for cards appearing */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.transform {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

/* Custom scrollbar for dark theme */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
}

::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

a:hover {
    transform: translateY(-1px);
}
</style>
