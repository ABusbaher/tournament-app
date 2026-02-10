<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import FileInput from "@/Components/FileInput.vue";
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {computed, onMounted, reactive, ref, watch} from 'vue';
import {useVuelidate} from '@vuelidate/core'
import {helpers, integer, maxLength, maxValue, minLength, minValue, required} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";

const addTeam = ref(false);
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;
tournamentStore.getByTournamentById(tournamentId);
const tournamentType = computed(() => tournamentStore.getType);

const state = reactive({
    name: '',
    shorten_name: '',
    negative_points: '',
    image: null,
});
const imageExtensions = ['jpeg', 'png', 'jpg', 'webp'];
const fileSizeLimit = 2 * 1024 * 1024;
const validImg = (value) => {
    if (!value) return true;
    const extension = value.name.split('.').pop();
    return imageExtensions.includes(extension.toLowerCase());
}

const validImgSize = (value) => {
    if (!value) return true;
    return value.size <= fileSizeLimit;
}

const rules = {
    name: { required, minLength: minLength(3) },
    shorten_name: { required, minLength: minLength(2), maxLength: maxLength(4) },
    negative_points: { integer, minValue: minValue(-100), maxValue: maxValue(0) },
    image: {
        validImg: helpers.withMessage('Tip fajla nije podržan', validImg),
        validImgSize: helpers.withMessage('Slika ne može biti veća od ' + fileSizeLimit + ' bajtova', validImgSize)
    }
}

const v$ = useVuelidate(rules, state)
const maxTeamError = ref('');
const openModal = async () => {
    await tournamentStore.getByTournamentById(tournamentId);
    addTeam.value = true;
};
const emit = defineEmits(['teamCreated']);

const config = {
    headers: {
        'Content-Type': 'multipart/form-data',
    },
};

onMounted(async () => {
    try {
        await tournamentStore.getByTournamentById(tournamentId);
    } catch (error) {
        console.log(error);
    }
});

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.post(`/api/tournaments/${tournamentId}/teams`, {
        name: state.name,
        shorten_name: state.shorten_name,
        negative_points: tournamentType.value === 'league' ? state.negative_points : null,
        tournament_id: tournamentId,
        ...(state.image && { image: state.image }),
    }, config).then(response => {
        emit('teamCreated', response.data);
        closeModal();
    })
        .catch(error => {
                maxTeamError.value = error.response.data.message;
                console.log(error.response.data);
        });
};

const closeModal = () => {
    addTeam.value = false;
    state.name = '';
    state.shorten_name = '';
    state.negative_points = '';
    maxTeamError.value = '';
    state.image = null;
};
</script>

<template>
    <section class="space-y-6">
        <!-- Modern Button -->
        <button
            @click="openModal"
            class="inline-flex items-center px-6 py-3 font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white border-green-500/30 hover:border-green-400/50"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Dodaj Tim
        </button>

        <Modal :show="addTeam" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 via-emerald-500/10 to-teal-500/10 animate-pulse"></div>

                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Dodaj Tim
                                </h2>
                                <p class="text-gray-400 text-sm">Kreirajte novi tim za turnir</p>
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
                    <!-- Team Name -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Ime tima
                        </label>
                        <div class="relative">
                            <input
                                v-model="state.name"
                                type="text"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300"
                                placeholder="Unesite ime tima"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.name.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Shortened Name -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Skraćeno ime tima
                        </label>
                        <div class="relative">
                            <input
                                v-model="state.shorten_name"
                                type="text"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all duration-300"
                                placeholder="Kratko ime tima (2 do 4 slova)"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.shorten_name.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Negative Points (League Only) -->
                    <div v-if="tournamentType === 'league'" class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Negativni poeni (opciono)
                        </label>
                        <div class="relative">
                            <input
                                v-model="state.negative_points"
                                type="number"
                                min="-100"
                                max="0"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500/50 transition-all duration-300"
                                placeholder="Unesite negativne poene"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.negative_points.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Team Logo -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Logo tima
                        </label>
                        <div class="relative">
                            <div class="border-2 border-dashed border-gray-600/50 rounded-lg p-6 hover:border-gray-500/50 transition-colors duration-300">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-400 mt-4">
                                        <label for="file-upload" class="relative cursor-pointer bg-gray-700/50 rounded-md font-medium text-green-400 hover:text-green-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>Otpremi fajl</span>
                                            <input
                                                id="file-upload"
                                                name="file-upload"
                                                type="file"
                                                class="sr-only"
                                                accept=".jpeg,.jpg,.png,.webp"
                                                @change="state.image = $event.target.files[0]"
                                            />
                                        </label>
                                        <p class="pl-1">ili prevuci i spusti</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        PNG, JPG, JPEG, WEBP do 2MB
                                    </p>
                                </div>
                            </div>
                            <div v-if="state.image" class="mt-4 p-3 bg-green-500/10 border border-green-500/30 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-green-400">{{ state.image.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.image.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="maxTeamError" class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-red-400">{{ maxTeamError }}</p>
                        </div>
                    </div>

                    <!-- Info Message -->
                    <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-green-400">
                                <span v-if="tournamentType === 'league'">Tim će biti dodat u liga turnir</span>
                                <span v-else-if="tournamentType === 'elimination'">Tim će biti dodat u eliminacijski turnir</span>
                                <span v-else>Izaberite tip turnira za više informacija</span>
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
                            @click="submitForm"
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-green-500/30 hover:border-green-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Dodaj Tim</span>
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

/* Input focus animations */
input:focus {
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    border-color: #22c55e !important;
    background: #374151 !important;
}

/* Remove white background on input hover/focus */
input:hover {
    background: #374151 !important;
    border-color: #4b5563 !important;
}

input {
    background: #374151 !important;
    color: #f3f4f6 !important;
}

input::placeholder {
    color: #9ca3af !important;
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

/* File upload styling */
input[type="file"] {
    background: transparent !important;
}

/* Animation for info message */
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

/* Remove spinner arrows from number inputs in modal */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>
