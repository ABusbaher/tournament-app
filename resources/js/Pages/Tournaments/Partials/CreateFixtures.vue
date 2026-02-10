<script setup>
import BigButton from "@/Components/BigButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {onMounted, ref} from "vue";
import {useTournamentStore} from "@/stores/Tournament.js";
import Modal from "@/Components/Modal.vue";

const modalOpened = ref(false);
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;
tournamentStore.getByTournamentById(tournamentId);
const tournamentType = ref(tournamentStore.getType);
const error403 = ref('');

const openModal = () => {
    modalOpened.value = true;
};

const closeModal = () => {
    modalOpened.value = false;
    error403.value = '';
};
let url =  ref('');

const emit = defineEmits(['fixturesCreated']);

const createFixtures = async () => {
    if (error403.value) return;
    if (tournamentType.value === 'league') {
        url.value = `/api/tournaments/${tournamentId}/games`;
    } else if (tournamentType.value === 'elimination') {
        url.value = `/api/tournaments/${tournamentId}/elimination-games`;
    }
    try {
        const response = await axios.post(url.value, {
            tournament_id: tournamentId
        });
        emit('fixturesCreated', response.data);
        await closeModal();
        window.location.href = response.data.redirect_url;
    } catch (error) {
        if (error.response && error.response.status === 403) {
            error403.value = error.response.data.message;
        } else {
            console.log(error.response.data);
        }
    }
};

onMounted(async () => {
    try {
        await tournamentStore.getByTournamentById(tournamentId);
        tournamentType.value = tournamentStore.getType;
    } catch (error) {
        console.log(error);
    }
});

</script>

<template>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="text-center">
            <h2 class="text-xl font-bold text-white mb-2">
                Kreiraj Raspored
            </h2>
            <p class="text-gray-400 text-sm">
                Nastavite sa kreiranjem nasumičnog rasporeda utakmica
            </p>
        </div>

        <!-- Create Fixtures Button -->
        <div class="flex justify-center">
            <button
                @click="openModal"
                class="inline-flex items-center px-8 py-4 font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white border-purple-500/30 hover:border-purple-400/50"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Kreiraj raspored
            </button>
        </div>

        <!-- Confirmation Modal -->
        <Modal :show="modalOpened" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 via-pink-500/10 to-rose-500/10 animate-pulse"></div>

                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Kreiraj Raspored
                                </h2>
                                <p class="text-gray-400 text-sm">Potvrdite kreiranje rasporeda utakmica</p>
                            </div>
                        </div>
                        <button
                            @click="closeModal"
                            class="text-gray-400 hover:text-white transition-colors duration-200 p-2 rounded-lg hover:bg-gray-700/50"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="relative p-8 space-y-6">
                    <!-- Warning Message -->
                    <div class="p-6 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-yellow-400 mb-2">
                                    Važna napomena
                                </h3>
                                <p class="text-sm text-yellow-300">
                                    Nakon potvrde nećete moći dodavati/brisati timove ili menjati broj rundi i tip turnira.
                                    Ova akcija je nepovratna.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tournament Type Info -->
                    <div class="p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-purple-400">
                                <span v-if="tournamentType === 'league'">Kreiraće se raspored za liga turnir</span>
                                <span v-else-if="tournamentType === 'elimination'">Kreiraće se raspored za eliminacijski turnir</span>
                                <span v-else>Tip turnira nije određen</span>
                            </p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="error403" class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-red-400">{{ error403 }}</p>
                        </div>
                    </div>

                    <!-- Info Message -->
                    <div class="p-4 bg-gray-500/10 border border-gray-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-400">
                                Raspored će biti generisan automatski na osnovu broja timova i tipa turnira.
                                Možete ga kasnije prilagoditi po potrebi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="relative p-8 border-t border-gray-600/30 bg-gray-800/50">
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="closeModal"
                            class="px-6 py-3 text-gray-300 hover:text-white bg-gray-700/50 hover:bg-gray-600/50 border border-gray-600/50 hover:border-gray-500/50 rounded-lg font-medium transition-all duration-300 transform hover:scale-105"
                        >
                            Otkaži
                        </button>
                        <button
                            @click="createFixtures"
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-purple-500/30 hover:border-purple-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Kreiraj Raspored</span>
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
/* Custom scrollbar for modal */
::-webkit-scrollbar {
    width: 6px;
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

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

/* Modal backdrop blur */
:deep(.fixed) {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.5);
}

/* Animation for warning message */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Warning icon animation */
.w-6.h-6 {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}
</style>
