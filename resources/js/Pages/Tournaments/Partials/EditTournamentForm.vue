<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from "@/Components/SelectInput.vue";
import {reactive, onMounted, ref, watch} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minLength, numeric, minValue, maxValue, requiredIf} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";

const props = defineProps({
    tournamentId: {
        type: Number,
        required: true,
    },
});

const tournamentStore = useTournamentStore();
tournamentStore.setIdFromUrl();
const tournamentId = tournamentStore.getId;

const tournamentState = reactive({
    tournamentName: tournamentStore.getName,
    tournamentRoundOptions: tournamentStore.roundOptions,
    tournamentRounds: tournamentStore.getRounds,
    tournamentType: tournamentStore.getType,
    tournamentTypes: tournamentStore.types,
})
const rulesForTournament = () => ( {
    tournamentName: { required, minLength: minLength(3) },
    tournamentRounds: { requiredIfLeague: requiredIf(() => tournamentState.tournamentType === 'league'), numeric, minValue: minValue(1), maxValue: maxValue(4) },
    tournamentType: { required }
});

const v$ = useVuelidate(rulesForTournament(), tournamentState);
const emit = defineEmits(['tournamentEdited']);
const error403 = ref('');

const editTournament = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.put(`/api/tournaments/${tournamentId}`, {
        name: tournamentState.tournamentName,
        rounds: tournamentState.tournamentRounds,
        type: tournamentState.tournamentType,
        tournament_id: tournamentId
    }).then(response => {
        const updatedTournament = response.data;
        tournamentStore.setType(tournamentState.tournamentType);
        emit('tournamentEdited', updatedTournament);
    })
    .catch(error => {
        if (error.response && error.response.status === 403) {
            error403.value = 'Informacije o turniru se ne mogu ažurirati jer je raspored već kreiran!';
            setTimeout(() => {error403.value = '';}, 5000);
            tournamentState.tournamentName = tournamentStore.getName;
            tournamentState.tournamentRounds = tournamentStore.getRounds.toString();
            tournamentState.tournamentType = tournamentStore.getType;
        } else {
            console.log(error.response.data);
        }
    });
}

watch(() => tournamentState.tournamentType, (newType) => {
    if (newType !== 'league') {
        tournamentState.tournamentRounds = '1';
    }
});


onMounted(async() => {
    await tournamentStore.setIdFromUrl();
    tournamentState.tournamentId = tournamentStore.getId;
    tournamentState.tournamentName = tournamentStore.getName;
    tournamentState.tournamentRounds = tournamentStore.getRounds.toString();
    tournamentState.tournamentType = tournamentStore.getType;
});
</script>

<template>
    <div class="space-y-6">
        <!-- Warning Message -->
        <div class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-sm text-yellow-400">
                    <strong>Važno:</strong> Nećete moći kasnije da menjate ove postavke
                </p>
            </div>
        </div>

        <!-- Tournament Name -->
        <div class="space-y-3">
            <label class="block text-sm font-semibold text-gray-300">
                Naziv turnira
            </label>
            <div class="relative">
                <input
                    v-model="tournamentState.tournamentName"
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
            <div class="input-errors" v-for="error of v$.tournamentName.$errors" :key="error.$uid">
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
                    v-model="tournamentState.tournamentType"
                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all duration-300 appearance-none"
                >
                    <option value="" disabled class="bg-gray-700 text-gray-400">Izaberite tip turnira</option>
                    <option 
                        v-for="type in tournamentState.tournamentTypes" 
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
            <div class="input-errors" v-for="error of v$.tournamentType.$errors" :key="error.$uid">
                <InputError :message="error.$message" />
            </div>
        </div>

        <!-- Tournament Rounds (League Only) -->
        <div v-if="tournamentState.tournamentType === 'league'" class="space-y-3">
            <label class="block text-sm font-semibold text-gray-300">
                Broj rundi
            </label>
            <div class="relative">
                <select
                    v-model="tournamentState.tournamentRounds"
                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-300 appearance-none"
                >
                    <option value="" disabled class="bg-gray-700 text-gray-400">Izaberite broj rundi</option>
                    <option 
                        v-for="round in tournamentState.tournamentRoundOptions" 
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
            <div class="input-errors" v-for="error of v$.tournamentRounds.$errors" :key="error.$uid">
                <InputError :message="error.$message" />
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
        <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-400">
                    <span v-if="tournamentState.tournamentType === 'league'">Liga turnir - timovi se bore za najbolju poziciju u tabeli</span>
                    <span v-else-if="tournamentState.tournamentType === 'elimination'">Eliminacijski turnir - timovi se bore u sistemu ispadanja</span>
                    <span v-else>Izaberite tip turnira za više informacija</span>
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center pt-4">
            <button 
                @click="editTournament"
                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl border border-blue-500/30 hover:border-blue-400/50 flex items-center space-x-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Izmeni Turnir</span>
            </button>
        </div>
    </div>
</template>

<style scoped>
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

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}
</style>
