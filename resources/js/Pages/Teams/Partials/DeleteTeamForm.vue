<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {ref} from 'vue';
import {useTournamentStore} from "@/stores/Tournament.js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const confirmingTournamentDeletion = ref(false);
const props = defineProps({
    teamId: {
        type: Number,
        required: true,
    },
});
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;

const openModal = () => {
    confirmingTournamentDeletion.value = true;
};

const closeModal = () => {
    errorMsg.value = '';
    confirmingTournamentDeletion.value = false;
};
const errorMsg = ref('');

const emit = defineEmits(['teamDeleted']);

const deleteTeam = () => {
    if (errorMsg.value) return;
    axios.delete(`/api/tournaments/${tournamentId}/teams/${props.teamId}`, {
    }).then(response => {
        emit('teamDeleted', props.tournamentId);
        closeModal();
    })
        .catch(error => {
            console.log(error.response.data.message);
            errorMsg.value = error.response.data.message;
            console.log(error.response);
        });
};
</script>

<template>
    <section class="inline-flex space-x-2">
        <!-- Modern Delete Button -->
        <button 
            @click="openModal" 
            class="inline-flex items-center px-3 py-2 font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg border bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white border-red-500/30 hover:border-red-400/50"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>

        <Modal :show="confirmingTournamentDeletion" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 via-pink-500/10 to-rose-500/10 animate-pulse"></div>
                
                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-red-600 to-pink-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Obriši Tim
                                </h2>
                                <p class="text-gray-400 text-sm">Potvrdite brisanje tima</p>
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
                    <div class="p-6 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-red-400 mb-2">
                                    Da li ste sigurni da želite da obrišete tim?
                                </h3>
                                <p class="text-sm text-red-300">
                                    Ova akcija je nepovratna. Tim će biti trajno obrisan iz turnira, 
                                    uključujući sve povezane utakmice i rezultate.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMsg" class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-red-400">{{ errorMsg }}</p>
                        </div>
                    </div>

                    <!-- Info Message -->
                    <div class="p-4 bg-gray-500/10 border border-gray-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-400">
                                Pre brisanja tima, proverite da li su sve utakmice završene i da li je potrebno sačuvati bilo kakve podatke.
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
                            @click="deleteTeam"
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-red-500/30 hover:border-red-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Obriši Tim</span>
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </section>
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
</style>
