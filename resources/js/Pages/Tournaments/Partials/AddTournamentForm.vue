<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from "@/Components/SelectInput.vue";
import { ref, defineEmits, reactive } from 'vue';
import { useVuelidate } from '@vuelidate/core'
import {required, minLength, minValue, maxValue, integer} from '@vuelidate/validators'

const addTournament = ref(false);

const state = reactive({
    name: '',
    rounds: '',
    type: '',
})
const rules = {
    name: { required, minLength: minLength(3) },
    rounds: { required, integer, minValue: minValue(1), maxValue: maxValue(4) },
    type: { required }
}

const v$ = useVuelidate(rules, state)

const data = {
    types: [
        { value: 'league', label: 'League' },
        { value: 'elimination', label: 'Elimination (Cup)' },
        { value: 'group+elimination', label: 'Group+Elimination' }
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
</script>

<template>
    <section class="space-y-6">
        <PrimaryButton @click="openModal">Add Tournament</PrimaryButton>

        <Modal :show="addTournament" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Add Tournament
                </h2>

                <div :class="['mt-6', { error: v$.name.$errors.length }]">
                    <InputLabel for="name" value="Tournament name" />

                    <TextInput
                        id="name"
                        ref="nameInput"
                        v-model="state.name"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="Tournament name"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.name.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div :class="['mt-6', { error: v$.rounds.$errors.length }]">
                    <InputLabel for="rounds" value="Number of rounds" />

                    <TextInput
                        id="rounds"
                        ref="nameInput"
                        v-model="state.rounds"
                        type="number"
                        min="1"
                        max="4"
                        class="mt-1 block w-3/4"
                        placeholder="1 - 4"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.rounds.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6">
                    <InputLabel for="types" value="Choose type of tournament"/>

                    <SelectInput
                        id="types"
                        name="types"
                        v-model="state.type"
                        :options="data.types"
                        class="mt-1 block w-3/4"
                    />
                    <div class="input-errors mt-2" v-for="error of v$.type.$errors" :key="error.$uid">
                        <InputError :message="error.$message" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        @click="submitForm"
                    >
                        Add Tournament
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
