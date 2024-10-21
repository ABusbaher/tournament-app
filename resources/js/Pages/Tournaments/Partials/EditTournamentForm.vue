<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from "@/Components/SelectInput.vue";
import {reactive, onMounted, ref} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minLength, numeric, minValue, maxValue} from '@vuelidate/validators'
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
    tournamentRounds: { required, numeric, minValue: minValue(1), maxValue: maxValue(4) },
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
        emit('tournamentEdited', updatedTournament);
    })
        .catch(error => {
            if (error.response && error.response.status === 403) {
                error403.value = 'Tournament information can not be updated, since fixtures has been already created!';
                setTimeout(() => {error403.value = '';}, 5000);
                tournamentState.tournamentName = tournamentStore.getName;
                tournamentState.tournamentRounds = tournamentStore.getRounds.toString();
                tournamentState.tournamentType = tournamentStore.getType;
            } else {
                console.log(error.response.data);
            }
        });
}



onMounted(async() => {
    await tournamentStore.setIdFromUrl();
    tournamentState.tournamentId = tournamentStore.getId;
    tournamentState.tournamentName = tournamentStore.getName;
    tournamentState.tournamentRounds = tournamentStore.getRounds.toString();
    tournamentState.tournamentType = tournamentStore.getType;
});
</script>

<template>
    <div class="w-3/5 mx-auto mt-10">
        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center">
            Edit Tournament (you can not change later this settings)
        </h4>

        <div :class="['mt-6', { error: v$.tournamentName.$errors.length }]">
            <InputLabel for="tournamentName" value="Tournament name" />

            <TextInput
                id="tournamentName"
                ref="nameInput"
                v-model="tournamentState.tournamentName"
                type="text"
                class="mt-1 block w-full"
                placeholder="Tournament name"
            />
            <div class="input-errors mt-2" v-for="error of v$.tournamentName.$errors" :key="error.$uid">
                <InputError :message="error.$message" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <InputLabel for="types" value="Choose type of tournament"/>

            <SelectInput
                id="types"
                name="types"
                v-model="tournamentState.tournamentType"
                :options="tournamentState.tournamentTypes"
                class="mt-1 block w-full"
            />
            <div class="input-errors mt-2" v-for="error of v$.tournamentType.$errors" :key="error.$uid">
                <InputError :message="error.$message" class="mt-2" />
            </div>
        </div>

        <div v-if="tournamentState.tournamentType === 'league'" :class="['mt-6', { error: v$.tournamentRounds.$errors.length }]">
            <InputLabel for="rounds" value="Number of rounds" />

            <SelectInput
                id="rounds"
                name="rounds"
                v-model="tournamentState.tournamentRounds"
                :options="tournamentState.tournamentRoundOptions"
                class="mt-1 block w-full"
            />
            <div class="input-errors mt-2" v-for="error of v$.tournamentRounds.$errors" :key="error.$uid">
                <InputError :message="error.$message" class="mt-2" />
            </div>
        </div>

        <div v-if="error403" class="mt-2">
            <p class="text-sm text-red-600 dark:text-red-400">
                {{ error403 }}
            </p>
        </div>

        <div class="mt-6 pb-10 flex justify-center">
            <PrimaryButton
                class="ml-3"
                @click="editTournament"
            >
                Edit Tournament
            </PrimaryButton>
        </div>
    </div>
</template>
