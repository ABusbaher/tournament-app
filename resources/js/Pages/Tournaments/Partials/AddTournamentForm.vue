<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from "@/Components/SelectInput.vue";
import {ref, reactive, watch} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minLength, minValue, maxValue, integer, requiredIf} from '@vuelidate/validators'

const addTournament = ref(false);

const state = reactive({
    name: '',
    rounds: '',
    type: '',
})
const rules = {
    name: { required, minLength: minLength(3) },
    rounds: {
        requiredIfLeague: requiredIf(() => state.type === 'league'),
        integer,
        minValue: minValue(1),
        maxValue: maxValue(2)
    },
    type: { required }
}

const v$ = useVuelidate(rules, state)

const data = {
    types: [
        { value: 'league', label: 'Liga' },
        { value: 'elimination', label: 'Kup' },
        // { value: 'group+elimination', label: 'Group+Elimination' }
    ],
    roundOptions: [
        { value: '1', label: '1' },
        { value: '2', label: '2' },
    ],
};

const openModal = () => {
    addTournament.value = true;
};
const emit = defineEmits(['tournamentCreated']);

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.post('/api/tournaments', {
        name: state.name,
        rounds: state.rounds,
        type: state.type,
    }).then(response => {
        emit('tournamentCreated', response.data['tournament']);
        closeModal();
    })
        .catch(error => {
            console.log(error.response.data);
        });
};

const closeModal = () => {
    addTournament.value = false;
    state.rounds = '';
    state.name = '';
    state.type = ''
};

watch(() => state.type, (newType) => {
    if (newType !== 'league') {
        state.rounds = '1';
    }
});

</script>

<template>
    <section class="space-y-6">
        <!-- Modern Button -->
        <button
            @click="openModal"
            class="inline-flex items-center px-6 py-3 font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white border-blue-500/30 hover:border-blue-400/50"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Dodaj turnir
        </button>

        <Modal :show="addTournament" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 animate-pulse"></div>

                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Dodaj turnir
                                </h2>
                                <p class="text-gray-400 text-sm">Kreirajte novi turnir</p>
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
                    <!-- Tournament Name -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Naziv turnira
                        </label>
                        <div class="relative">
                            <input
                                v-model="state.name"
                                type="text"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                                placeholder="Unesite naziv turnira"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.name.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Tournament Type -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Tip turnira
                        </label>
                        <div class="relative">
                            <select
                                v-model="state.type"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all duration-300 appearance-none"
                            >
                                <option value="" disabled class="bg-gray-700 text-gray-400">Izaberite tip turnira</option>
                                <option
                                    v-for="type in data.types"
                                    :key="type.value"
                                    :value="type.value"
                                    class="bg-gray-700 text-white"
                                >
                                    {{ type.label }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.type.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Rounds (League Only) -->
                    <div v-if="state.type === 'league'" class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Broj rundi
                        </label>
                        <div class="relative">
                            <select
                                v-model="state.rounds"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-300 appearance-none"
                            >
                                <option value="" disabled class="bg-gray-700 text-gray-400">Izaberite broj rundi</option>
                                <option
                                    v-for="round in data.roundOptions"
                                    :key="round.value"
                                    :value="round.value"
                                    class="bg-gray-700 text-white"
                                >
                                    {{ round.label }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="input-errors" v-for="error of v$.rounds.$errors" :key="error.$uid">
                            <InputError :message="error.$message" />
                        </div>
                    </div>

                    <!-- Info Message -->
                    <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-400">
                                <span v-if="state.type === 'league'">Liga turnir - timovi se bore za najbolju poziciju u tabeli</span>
                                <span v-else-if="state.type === 'elimination'">Eliminacijski turnir - timovi se bore u sistemu ispadanja</span>
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
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Dodaj turnir</span>
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
input:focus, select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6 !important;
    background: #374151 !important;
}

/* Remove white background on input hover/focus */
input:hover, select:hover {
    background: #374151 !important;
    border-color: #4b5563 !important;
}

input, select {
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

/* Select dropdown styling */
select option {
    background: #374151;
    color: #f3f4f6;
    padding: 8px;
}

select option:hover {
    background: #4b5563;
}

/* Disabled state styling */
select:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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
