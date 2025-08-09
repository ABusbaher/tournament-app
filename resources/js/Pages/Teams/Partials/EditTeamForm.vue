<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {ref, reactive, watch, computed} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minLength, helpers, maxLength, integer, minValue, maxValue} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";
import FileInput from "@/Components/FileInput.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const editTeam = ref(false);
const props = defineProps({
    teamId: {
        type: Number,
        required: true,
    },
});
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;
const tournamentType = computed(() => tournamentStore.getType);

const state = reactive({
    name: '',
    shorten_name: '',
    negative_points: '',
    image: null,
    previous_image: null,
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

const errorMsg = ref('');

const v$ = useVuelidate(rules, state)

const openModal = async () => {
    editTeam.value = true;
    await tournamentStore.getByTournamentById(tournamentId);
    await axios.get(`/api/tournaments/${tournamentId}/teams/${props.teamId}`)
        .then(response => {
            state.name = response.data['name'];
            state.shorten_name = response.data['shorten_name'];
            if (response.data['negative_points'] === 0 && tournamentType.value === 'league') {
                state.negative_points = '0'
            } else if (response.data['negative_points'] && tournamentType.value === 'league') {
                state.negative_points = response.data['negative_points'].toString()
            } else {
                state.negative_points = '';
            }
            state.previous_image = response.data['image_path'];
        })
        .catch(error => {
            console.log(error);
        });
};
const emit = defineEmits(['teamUpdated']);

const config = {
    headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json',
    },
};

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    let data = new FormData();
    data.append('_method', 'put');
    data.append('name', state.name);
    data.append('shorten_name', state.shorten_name);
    data.append('negative_points', state.negative_points);
    data.append('image', state.image ? state.image : '');
    axios.post(`/api/tournaments/${tournamentId}/teams/${props.teamId}`, data, config).then(response => {
        emit('teamUpdated', response.data);
        closeModal();
    })
        .catch(error => {
            errorMsg.value = error.response.data.message;
            console.log(error.response.data);
        });
};

const closeModal = () => {
    editTeam.value = false;
    state.name = '';
    state.shorten_name = '';
    state.negative_points = '';
    state.previous_image = null;
    state.image = null;
};</script>

<template>
    <section class="inline-flex space-x-2 mr-3">
        <!-- Modern Edit Button -->
        <button 
            @click="openModal" 
            class="inline-flex items-center px-3 py-2 font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg border bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white border-blue-500/30 hover:border-blue-400/50"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </button>

        <Modal :show="editTeam" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 animate-pulse"></div>
                
                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Izmeni Tim
                                </h2>
                                <p class="text-gray-400 text-sm">Ažurirajte informacije o timu</p>
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
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
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
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all duration-300"
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
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-300"
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
                        
                        <!-- Current Logo Display -->
                        <div v-if="state.previous_image" class="mb-4">
                            <p class="text-sm text-gray-400 mb-2">Trenutni logo:</p>
                            <div class="flex items-center space-x-4">
                                <img :src="state.previous_image" alt="Logo tima" 
                                     class="h-16 w-16 rounded-lg object-cover border-2 border-gray-600/50 shadow-lg"/>
                                <div class="text-sm text-gray-400">
                                    <p>Postojeći logo tima</p>
                                    <p class="text-xs text-gray-500">Izaberite novi fajl da ga zamenite</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="relative">
                            <div class="border-2 border-dashed border-gray-600/50 rounded-lg p-6 hover:border-gray-500/50 transition-colors duration-300">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-400 mt-4">
                                        <label for="file-upload-edit" class="relative cursor-pointer bg-gray-700/50 rounded-md font-medium text-blue-400 hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Otpremi novi fajl</span>
                                            <input 
                                                id="file-upload-edit" 
                                                name="file-upload-edit" 
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
                            <div v-if="state.image" class="mt-4 p-3 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-blue-400">{{ state.image.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.image.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
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
                    <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-400">
                                <span v-if="tournamentType === 'league'">Tim je deo liga turnira</span>
                                <span v-else-if="tournamentType === 'elimination'">Tim je deo eliminacijskog turnira</span>
                                <span v-else>Informacije o turniru</span>
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
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Izmeni Tim</span>
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
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6 !important;
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
</style>
