<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {ref, defineEmits, reactive, toRefs} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minValue, maxValue, integer} from '@vuelidate/validators'
import {useTournamentStore} from "@/stores/Tournament.js";

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
});

const rules = {
    hostTeamScore: { required, integer, minValue: minValue(0), maxValue: maxValue(100) },
    guestTeamScore: { required, integer, minValue: minValue(0), maxValue: maxValue(100) },
}

const v$ = useVuelidate(rules, state)

const openModal = () => {
    modalOpened.value = true;
    axios.get(`/api/tournaments/${tournamentId}/games/${props.gameId}`)
        .then(response => {
            console.log(response.data);
            state.hostTeam = response.data.host_team.name;
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

const editedBefore = () => {
    editedScore.value ? 'Edit score' : 'Insert score';
};

const submitForm = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    let data = new FormData();
    data.append('_method', 'patch');
    data.append('host_goals', state.hostTeamScore);
    data.append('guest_goals', state.guestTeamScore);
    axios.post(`/api/tournaments/${tournamentId}/games/${props.gameId}`, data, config).then(response => {
        emit('scoreUpdated', response.data);
        closeModal();
    })
        .catch(error => {
            console.log(error.response.data);
        });
};

const closeModal = () => {
    modalOpened.value = false;
};
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton class="mt-3" @click="openModal">
            {{ props.editedScore ? 'Edit score' : 'Insert score' }}
        </PrimaryButton>
        <Modal :show="modalOpened" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ props.editedScore ? 'Edit score' : 'Insert score' }}
                </h2>

                <div :class="['mt-6', { error: v$.hostTeamScore.$errors.length }]">
                    <InputLabel for="hostTeamScore" :value="state.hostTeam + ' score'" />
                    <TextInput
                        id="hostTeamScore"
                        ref="nameInput"
                        v-model="state.hostTeamScore"
                        type="number"
                        min="0"
                        max="100"
                        class="mt-1 block w-3/4"
                        placeholder="Home team score"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.hostTeamScore.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.guestTeamScore.$errors.length }]">
                    <InputLabel for="guestTeamScore" :value="state.guestTeam + ' score'" />
                    <TextInput
                        id="guestTeamScore"
                        ref="nameInput"
                        v-model="state.guestTeamScore"
                        type="number"
                        min="0"
                        max="100"
                        class="mt-1 block w-3/4"
                        placeholder="Guest team score"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.guestTeamScore.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton class="ml-3" @click="submitForm">
                        {{ props.editedScore ? 'Edit score' : 'Insert score' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
