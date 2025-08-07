<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {ref, reactive, toRefs, computed} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {minValue, maxValue, integer, requiredUnless} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {useDateTimeFormatter} from "@/composables/useDateTimeFormatter.js";

const modalOpened = ref(false);
const props = defineProps({
    gameId: {
        type: Number,
        required: true,
    },
    editedScore: {
        type: Boolean,
        nullable: true,
        default: null
    }
});
const tournamentStore = useTournamentStore();
const tournamentId = tournamentStore.getId;

const state = reactive({
    hostTeam: '',
    hostTeamScore: '',
    guestTeam: '',
    guestTeamScore: '',
    gameTime: null,
});

const rules = {
     hostTeamScore: {
         requiredIfLeague: requiredUnless(() => state.guestTeamScore === ''),
         integer, minValue: minValue(0), maxValue: maxValue(100)
     },
     guestTeamScore: {
         requiredIfLeague: requiredUnless(() => state.hostTeamScore === ''),
         integer, minValue: minValue(0), maxValue: maxValue(100)
     },
    gameTime: {},
}
const date = ref(new Date());
const { formatDate } = useDateTimeFormatter();
const errorMsg = ref('');

// const game_time = ref();
const v$ = useVuelidate(rules, state)

const openModal = () => {
    modalOpened.value = true;
    axios.get(`/api/tournaments/${tournamentId}/games/${props.gameId}`)
        .then(response => {
            state.hostTeam = response.data.host_team.name;
            state.gameTime = response.data.game_time?.toString();
            if (response.data.host_goals === 0) {
                state.hostTeamScore = '0'
            }else if (response.data.host_goals) {
                state.hostTeamScore = response.data.host_goals.toString()
            }else{
                state.hostTeamScore = '';
            }
            state.guestTeam = response.data.guest_team.name;
            if (response.data.guest_goals === 0) {
                state.guestTeamScore = '0'
            }else if (response.data.guest_goals) {
                state.guestTeamScore = response.data.guest_goals.toString()
            }else{
                state.guestTeamScore = '';
            }
        })
        .catch(error => {
            console.log(error);
        });
};
const emit = defineEmits(['scoreUpdated']);

const config = {
    headers: {
        'Accept': 'application/json',
    },
};
const { editedScore } = toRefs(props);

const getScoreButtonText = computed(() => {
    return editedScore.value ? 'Izmeni informacije o utakmici' : 'Unesi informacije o utakmici';
});

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    let data = new FormData();
    data.append('_method', 'patch');
    data.append('host_goals', state.hostTeamScore);
    data.append('guest_goals', state.guestTeamScore);
    if (state.gameTime) {
        data.append('game_time', new Date(state.gameTime).toISOString());
    }
    axios.post(`/api/tournaments/${tournamentId}/games/${props.gameId}`, data, config).then(response => {
        emit('scoreUpdated', response.data);
        closeModal();
    })
        .catch(error => {
            errorMsg.value = error.response.data.message;
            console.log(error.response.data);
        });
};

const closeModal = () => {
    errorMsg.value = '';
    modalOpened.value = false;
};
</script>

<template>
    <section class="space-y-6">
        <button
            @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            {{ getScoreButtonText }}
        </button>

        <Modal :show="modalOpened" @close="closeModal">
            <!-- Modern Modal Content -->
            <div class="relative bg-gradient-to-br from-gray-800 via-gray-700 to-gray-800 rounded-2xl shadow-2xl border border-gray-600/30 backdrop-blur-sm overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 animate-pulse"></div>

                <!-- Header -->
                <div class="relative p-8 border-b border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    {{ getScoreButtonText }}
                                </h2>
                                <p class="text-gray-400 text-sm">Ažurirajte rezultat utakmice</p>
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
                    <!-- Game Time Section -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">
                            Vreme utakmice
                        </label>
                        <div class="relative">
                            <VueDatePicker
                                v-model="state.gameTime"
                                time-picker-inline
                                :format="formatDate"
                                class="modern-datepicker"
                            />
                            <div class="input-errors mt-2" v-for="error of v$.gameTime.$errors" :key="error.$uid">
                                <InputError :message="error.$message" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Score Inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Host Team Score -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-300">
                                {{ state.hostTeam }} - Golovi
                            </label>
                            <div class="relative">
                                <input
                                    v-model="state.hostTeamScore"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                                    placeholder="Unesite broj golova"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="input-errors" v-for="error of v$.hostTeamScore.$errors" :key="error.$uid">
                                <InputError :message="error.$message" />
                            </div>
                        </div>

                        <!-- Guest Team Score -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-300">
                                {{ state.guestTeam }} - Golovi
                            </label>
                            <div class="relative">
                                <input
                                    v-model="state.guestTeamScore"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-300"
                                    placeholder="Unesite broj golova"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="input-errors" v-for="error of v$.guestTeamScore.$errors" :key="error.$uid">
                                <InputError :message="error.$message" />
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
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ getScoreButtonText }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </section>
</template>

<style scoped>
/* Modern DatePicker Styling */
:deep(.dp--menu-wrapper) {
    position: relative;
    top: 20px !important;
    z-index: 99999;
    left: 0 !important;
    background: #1f2937 !important;
    border: 1px solid #4b5563 !important;
    border-radius: 12px !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

:deep(.dp--tp-wrap) {
    max-width: 100%;
    background: #1f2937 !important;
    border-radius: 12px !important;
}

:deep(.dp--tp-col) {
    background: #374151 !important;
    border-radius: 8px !important;
    margin: 4px !important;
}

:deep(.dp--tp-col:hover) {
    background: #4b5563 !important;
}

:deep(.dp--tp-col.dp--tp-col-active) {
    background: #3b82f6 !important;
}

:deep(.dp--tp-col.dp--tp-col-active:hover) {
    background: #2563eb !important;
}

:deep(.dp--tp-col-value) {
    color: #f3f4f6 !important;
}

:deep(.dp--tp-col.dp--tp-col-active .dp--tp-col-value) {
    color: #ffffff !important;
}

/* Time picker specific styling */
:deep(.dp--tp-wrap .dp--tp-col) {
    background: #374151 !important;
    color: #f3f4f6 !important;
}

:deep(.dp--tp-wrap .dp--tp-col:hover) {
    background: #4b5563 !important;
    color: #ffffff !important;
}

:deep(.dp--tp-wrap .dp--tp-col.dp--tp-col-active) {
    background: #3b82f6 !important;
    color: #ffffff !important;
}

:deep(.dp--tp-wrap .dp--tp-col-value) {
    color: #f3f4f6 !important;
}

:deep(.dp--tp-wrap .dp--tp-col.dp--tp-col-active .dp--tp-col-value) {
    color: #ffffff !important;
}

/* Time picker inline container - white background for visibility */
:deep(.dp__time_picker_inline_container) {
    background: #ffffff !important;
    border-radius: 8px !important;
    padding: 8px !important;
    margin-top: 8px !important;
}

:deep(.dp__time_picker_inline_container .dp--tp-col) {
    background: #f3f4f6 !important;
    color: #374151 !important;
}

:deep(.dp__time_picker_inline_container .dp--tp-col:hover) {
    background: #e5e7eb !important;
    color: #1f2937 !important;
}

:deep(.dp__time_picker_inline_container .dp--tp-col.dp--tp-col-active) {
    background: #3b82f6 !important;
    color: #ffffff !important;
}

:deep(.dp__time_picker_inline_container .dp--tp-col-value) {
    color: #374151 !important;
}

:deep(.dp__time_picker_inline_container .dp--tp-col.dp--tp-col-active .dp--tp-col-value) {
    color: #ffffff !important;
}

/* Calendar styling */
:deep(.dp--calendar) {
    background: #1f2937 !important;
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-header) {
    background: #374151 !important;
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-header button) {
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-header button:hover) {
    background: #4b5563 !important;
    color: #ffffff !important;
}

:deep(.dp--calendar-nav) {
    background: #374151 !important;
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-nav button) {
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-nav button:hover) {
    background: #4b5563 !important;
    color: #ffffff !important;
}

:deep(.dp--calendar-nav button:disabled) {
    color: #6b7280 !important;
}

:deep(.dp--calendar-nav button:disabled:hover) {
    background: transparent !important;
    color: #6b7280 !important;
}

:deep(.dp--calendar-nav button:disabled svg) {
    color: #6b7280 !important;
}

:deep(.dp--calendar-nav button:disabled:hover svg) {
    color: #6b7280 !important;
}

/* Calendar days */
:deep(.dp--calendar-day) {
    background: #374151 !important;
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-day:hover) {
    background: #4b5563 !important;
    color: #ffffff !important;
}

:deep(.dp--calendar-day.dp--today) {
    background: #3b82f6 !important;
    color: #ffffff !important;
}

:deep(.dp--calendar-day.dp--selected) {
    background: #3b82f6 !important;
    color: #ffffff !important;
}

:deep(.dp--calendar-day.dp--disabled) {
    background: #1f2937 !important;
    color: #6b7280 !important;
}

:deep(.dp--calendar-day.dp--disabled:hover) {
    background: #1f2937 !important;
    color: #6b7280 !important;
}

/* Calendar weekdays */
:deep(.dp--calendar-weekday) {
    background: #374151 !important;
    color: #9ca3af !important;
}

/* Calendar month/year display */
:deep(.dp--calendar-month) {
    color: #f3f4f6 !important;
}

:deep(.dp--calendar-year) {
    color: #f3f4f6 !important;
}

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

/* Input focus animations - improved */
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
</style>
